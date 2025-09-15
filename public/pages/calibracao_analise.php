<?php
require __DIR__ . '/../../inc/db.php';
$pdo = get_db();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  echo '<div class="col-12"><div class="alert alert-warning">Calibração inválida.</div></div>'; return;
}

// Busca calibração + equipamento
$st = $pdo->prepare("
  SELECT c.*, e.nome AS equip_nome, e.tag AS equip_tag, e.idequipamento
    FROM calibracao c
    JOIN equipamento e ON e.idequipamento = c.equipamento
   WHERE c.idcalibracao = ?
");
$st->execute([$id]);
$cal = $st->fetch();
if (!$cal) { echo '<div class="col-12"><div class="alert alert-warning">Registro não encontrado.</div></div>'; return; }

// Faixas do equipamento
$fx = $pdo->prepare("
  SELECT grandeza, faixade, faixaate, criterio, calibrarem
    FROM equipamentofaixa
   WHERE equipamento = ?
   ORDER BY idequipamentofaixa ASC
");
$fx->execute([$cal['equipamento']]);
$faixas = $fx->fetchAll();
$equipId        = (int)$cal['equipamento'];
$equipNome      = (string)$cal['equip_nome'];
$equipTagSuffix = !empty($cal['equip_tag']) ? ' ('.$cal['equip_tag'].')' : '';
$provedor       = (string)$cal['nomeprovedor'];
$acreditado     = (string)$cal['acreditado']; // 'S' ou 'N'
$relatorioNum   = (string)$cal['relatorionumero'];
$dataRel        = $cal['datarelatorio']      ?? '';
$validadeRel    = $cal['validaderelatorio']  ?? '';
$resumo         = (string)$cal['resumoanalise'];

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

$arquivoInfo = !empty($cal['arquivo'])
  ? "Anexado: CERTIFICADO (pdf/imagem) em: {$cal['arquivo']}"
  : "Sem arquivo de certificado anexado.";

// --- HEREDOC apenas com variáveis simples ---
$prompt = <<<PROMPT
Você é um auditor técnico com experiência em ISO/IEC 17025. Analise criticamente o certificado/relatório de calibração ANEXADO e os dados do equipamento abaixo. Aponte conformidades e não conformidades com referências às cláusulas pertinentes da ISO/IEC 17025, observando rastreabilidade, incerteza, critérios de aceitação, identificação do equipamento, condições ambientais, escopo/acreditação, datas, assinaturas e declarações de conformidade.

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

INSTRUÇÕES DE SAÍDA (responda em português, estruturado):
1) Sumário executivo (3-5 linhas).
2) Itens de análise com referência à cláusula ISO/IEC 17025 quando aplicável:
   - Identificação do item calibrado
   - Rastreabilidade metrológica (padrões, certificados, cadeia)
   - Incerteza de medição (modelo, orçamento, declaração)
   - Critérios de aceitação e resultado (conformidade / decisão)
   - Condições ambientais e influência
   - Identificação do laboratório, assinaturas, versão, páginação
   - Escopo/acreditação aplicável (se declarado)
3) Não Conformidades / Oportunidades de melhoria (bullet points, referenciando cláusulas).
4) Recomendação final (conclusão prática).
5) Risco (Baixo/Médio/Alto) com breve justificativa.
PROMPT;


/* Salva análise (resposta da IA) se vier POST */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $analise = trim($_POST['analisedogpt'] ?? '');
  $up = $pdo->prepare("UPDATE calibracao SET analisedogpt = ? WHERE idcalibracao = ?");
  $up->execute([$analise ?: null, $id]);

  echo "<script>window.location.href='/?p=calibracao';</script>";
  exit;
}
?>

<div class="col-12">
  <div class="card shadow-sm">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
          <h2 class="h6 mb-1">Análise da IA • Calibração #<?= (int)$id ?></h2>
          <div class="text-muted small">
            Equipamento #<?= (int)$cal['equipamento'] ?> — <?= htmlspecialchars($cal['equip_nome']) ?>
            <?= $cal['equip_tag'] ? ' <span class="text-muted">('.htmlspecialchars($cal['equip_tag']).')</span>' : '' ?>
          </div>
        </div>
        <div class="d-flex gap-2">
          <a href="#" class="btn btn-secondary btn-sm" data-page="calibracao">
            <i class="bi bi-arrow-left me-1"></i>Voltar
          </a>
          <?php if (!empty($cal['arquivo'])): ?>
          <a href="/pages/calibracao_download.php?id=<?= (int)$id ?>" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-download me-1"></i>Baixar certificado
          </a>
          <?php endif; ?>
        </div>
      </div>

      <div class="mb-2 small text-muted">
        Copie o prompt abaixo e cole no seu provedor de IA. Se possível, anexe também o certificado baixado acima.<br>
        Ou utilize o botão "Analisar com IA" para tentar obter a análise automaticamente (limite de uso, pode falhar).
      </div>

      <div class="mb-3">
        <textarea id="prompt" class="form-control" rows="14" readonly><?= htmlspecialchars($prompt) ?></textarea>
        <div class="mt-2">
          <button class="btn btn-outline-secondary btn-sm" id="btnCopy">
            <i class="bi bi-clipboard-check me-1"></i>Copiar prompt
          </button>
          <button type="button" id="btnAskAI" class="btn btn-warning btn-sm">
            <i class="bi bi-robot me-1"></i>Analisar com IA
          </button>
          <span id="aiSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </div>
      </div>

      <hr>

      <form method="post" action="/pages/calibracao_analise.php?id=<?= (int)$id ?>">
        <label class="form-label">Análise</label>
        <textarea name="analisedogpt" class="form-control" rows="10" placeholder="Cole aqui a análise gerada pela IA..."><?= htmlspecialchars($cal['analisedogpt'] ?? '') ?></textarea>
        <div class="mt-3 d-flex gap-2">
          <button type="submit" class="btn btn-success">
            <i class="bi bi-save me-1"></i>Salvar
          </button>
          <button type="button" id="btnSalvarVoltar" class="btn btn-outline-success">
            <i class="bi bi-check2-circle me-1"></i>Salvar e voltar
          </button>
          <button type="button" id="btnDescartar" class="btn btn-outline-secondary">
            <i class="bi bi-x-circle me-1"></i>Descartar
          </button>
          <a href="#" class="btn btn-secondary" data-page="calibracao">Cancelar</a>
        </div>

      </form>
    </div>
  </div>
</div>

<script>
document.getElementById('btnCopy')?.addEventListener('click', async () => {
  const ta = document.getElementById('prompt');
  ta.select(); ta.setSelectionRange(0, 99999);
  try { await navigator.clipboard.writeText(ta.value); } catch {}
});
</script>
<script>
const btnAskAI   = document.getElementById('btnAskAI');
const aiSpinner  = document.getElementById('aiSpinner');
const taPrompt   = document.getElementById('prompt');
const taAnalise  = document.querySelector('textarea[name="analisedogpt"]');
const form       = document.querySelector('form[action^="/pages/calibracao_analise.php"]');

function setLoadingAI(loading){
  if (loading) {
    aiSpinner.classList.remove('d-none');
    btnAskAI?.setAttribute('disabled', 'disabled');
  } else {
    aiSpinner.classList.add('d-none');
    btnAskAI?.removeAttribute('disabled');
  }
}

btnAskAI?.addEventListener('click', async () => {
  try {
    setLoadingAI(true);
    const url = `/pages/calibracao_analise_api.php?id=<?php echo (int)$id; ?>`;
    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const data = await res.json();
    if (!data || data.status !== 'ok') throw new Error(data?.error || 'Falha na IA');

    // preenche o textarea com a resposta (sem salvar ainda)
    if (taAnalise) taAnalise.value = data.analise || '';
  } catch (err) {
    alert('Não foi possível obter a análise da IA: ' + err.message);
  } finally {
    setLoadingAI(false);
  }
});


document.getElementById('btnSalvarVoltar')?.addEventListener('click', () => {
  if (form) form.submit(); 
});
document.getElementById('btnDescartar')?.addEventListener('click', () => {
  if (taAnalise && confirm('Descartar alterações não salvas?')) taAnalise.value = '';
});
</script>


