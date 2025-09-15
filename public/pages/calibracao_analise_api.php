<?php
require __DIR__ . '/../../inc/db.php';
require __DIR__ . '/../../inc/config.php'; 

header('Content-Type: application/json; charset=utf-8');

if (!defined('OPENAI_API_KEY') || !OPENAI_API_KEY) {
  http_response_code(500);
  echo json_encode(['status'=>'error','error'=>'OPENAI_API_KEY não configurada']); exit;
}

$pdo = get_db();
$id  = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { http_response_code(400); echo json_encode(['status'=>'error','error'=>'ID inválido']); exit; }

// calibração + equipamento
$st = $pdo->prepare("
  SELECT c.*, e.nome AS equip_nome, e.tag AS equip_tag
    FROM calibracao c
    JOIN equipamento e ON e.idequipamento = c.equipamento
   WHERE c.idcalibracao = ?
");
$st->execute([$id]);
$cal = $st->fetch();
if (!$cal) { http_response_code(404); echo json_encode(['status'=>'error','error'=>'Calibração não encontrada']); exit; }

// faixas
$fx = $pdo->prepare("
  SELECT grandeza, faixade, faixaate, criterio, calibrarem
    FROM equipamentofaixa
   WHERE equipamento = ?
   ORDER BY idequipamentofaixa ASC
");
$fx->execute([$cal['equipamento']]);
$faixas = $fx->fetchAll();

// monta prompt (variáveis simples)
$equipId        = (int)$cal['equipamento'];
$equipNome      = (string)$cal['equip_nome'];
$equipTagSuffix = !empty($cal['equip_tag']) ? ' ('.$cal['equip_tag'].')' : '';

$linhasFaixas = [];
foreach ($faixas as $f) {
  $linhasFaixas[] = sprintf(
    "- %s | de: %s | até: %s | critério: %s | calibrar em: %s",
    $f['grandeza'] ?: '-',
    $f['faixade']  ?: '-',
    $f['faixaate'] ?: '-',
    $f['criterio'] ?: '-',
    trim((string)$f['calibrarem']) !== '' ? preg_replace('/\s+/', ' ', $f['calibrarem']) : '-'
  );
}
$blocoFaixas = $linhasFaixas ? implode("\n", $linhasFaixas) : "- (sem faixas cadastradas)";

$provedor       = (string)$cal['nomeprovedor'];
$acreditado     = (string)$cal['acreditado']; // S/N
$relatorioNum   = (string)$cal['relatorionumero'];
$dataRel        = $cal['datarelatorio']      ?? '';
$validadeRel    = $cal['validaderelatorio']  ?? '';
$resumo         = (string)$cal['resumoanalise'];
$arquivoInfo    = !empty($cal['arquivo'])
  ? "Anexado: CERTIFICADO (pdf/imagem) (não enviado no payload desta chamada)."
  : "Sem arquivo anexado neste registro.";

// HEREDOC
$prompt = <<<PROMPT
Você é um auditor técnico (ISO/IEC 17025). Analise criticamente os dados abaixo e produza uma avaliação conforme a norma, apontando conformidades e não conformidades com referência de cláusulas.

DADOS DO EQUIPAMENTO
- ID: {$equipId}
- Nome: {$equipNome}{$equipTagSuffix}
- Faixas declaradas:
{$blocoFaixas}

DADOS DA CALIBRAÇÃO
- Provedor: {$provedor}
- Acreditado: {$acreditado} (S/N)
- Nº do relatório: {$relatorioNum}
- Data do relatório: {$dataRel}
- Validade do relatório: {$validadeRel}
- Resumo interno: {$resumo}

{$arquivoInfo}

SAÍDA (em português, estruturado):
1) Sumário executivo (3-5 linhas).
2) Itens de análise com referência à cláusula ISO/IEC 17025 (identificação, rastreabilidade, incerteza, critérios, condições ambientais, identificação do laboratório, escopo/acreditação, declarações).
3) Não Conformidades / Oportunidades de melhoria (com cláusulas).
4) Recomendação final.
5) Risco (Baixo/Médio/Alto) com justificativa.
PROMPT;

// chamada à OpenAI (chat completions)
$payload = [
  "model" => "gpt-4o-mini",
  "temperature" => 0.2,
  "messages" => [
    ["role" => "system", "content" => "Você é um auditor de calibração com expertise na ISO/IEC 17025."],
    ["role" => "user", "content" => $prompt]
  ]
];

$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER => [
    "Content-Type: application/json",
    "Authorization: Bearer " . OPENAI_API_KEY
  ],
  CURLOPT_POST => true,
  CURLOPT_POSTFIELDS => json_encode($payload),
  CURLOPT_TIMEOUT => 60
]);
$res = curl_exec($ch);
if ($res === false) {
  $err = curl_error($ch);
  curl_close($ch);
  http_response_code(500);
  echo json_encode(['status'=>'error','error'=>"Erro cURL: $err"]); exit;
}
curl_close($ch);

$data = json_decode($res, true);
$analise = $data['choices'][0]['message']['content'] ?? null;
if (!$analise) {
  http_response_code(502);
  echo json_encode(['status'=>'error','error'=>'Sem conteúdo retornado pela IA']); exit;
}

// devolve para preencher o textarea
echo json_encode(['status'=>'ok','analise'=>$analise], JSON_UNESCAPED_UNICODE);
