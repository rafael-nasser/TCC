# Sistema de Gerenciamento de Equipamentos e Calibração
**TCC – Rafael Nasser**

Aplicação web simples para gestão de **equipamentos**, **locais**, **faixas de calibração**, **cronogramas** e **calibrações** (com upload de certificado), além de **Análise da IA** baseada na ISO/IEC 17025.  
Stack: **PHP 8**, **MySQL**, **Bootstrap 5**, **Fetch API**, rodando via **Docker Compose**.

---

## Funcionalidades
- **Equipamentos**: cadastro, edição, exclusão e associação a **locais**.
- **Faixas** por equipamento: grandeza, faixas, critério e pontos de calibração.
- **Cronograma**: previstos, realizados, cancelados; execução com 1 clique.
- **Calibração**: registro completo com **upload** de certificado (PDF/JPG/PNG) salvo **fora do `public/`**.
- **Análise da IA**: gera prompt e integra com a API (OpenAI) para análise conforme ISO/IEC 17025; permite revisar e salvar o parecer.

---

## Estrutura (resumo)
```
/public
  index.php
  /pages
    equipamento.php
    equipamento_dados.php
    equipamentofaixa*.php
    equipamentocronograma*.php
    calibracao.php
    calibracao_dados.php
    calibracao_download.php
    calibracao_analise.php
    cronograma.php
/inc
  db.php
  config.php
/storage             ← arquivos enviados (fora do public; criado no container)
docker-compose.yml
```

---

## Pré-requisitos
- Docker + Docker Compose
- Porta **8080** livre no host

---

## Subir com Docker
1. (Opcional, recomendado) Crie um arquivo `.env` na raiz:
   ```env
   DB_HOST=db
   DB_NAME=projeto_rafaelnasser
   DB_USER=tccuser
   DB_PASS=senhaFort3!
   DB_PORT=3306
   OPENAI_API_KEY=coloque_sua_chave_aqui 
   ```
2. Suba os containers:
   ```bash
   docker compose up -d --build
   ```
3. Acesse o sistema:
   - **http://localhost:8080**

---

## Banco de dados (acesso via container)

```bash
# entrar no container do DB
docker exec -it tcc-db bash

# acessar o MySQL (vai pedir a senha root definida nas variáveis)
mysql -u root -p${MYSQL_ROOT_PASSWORD}

# selecionar o banco
use projeto_rafaelnasser;
```

---

## Pasta de storage (para certificados)
Crie (uma vez) a pasta de armazenamento **fora do public** no container do app:

```bash
docker exec -it tcc-app bash -lc '
mkdir -p /var/www/storage/calibracoes &&
chown -R www-data:www-data /var/www/storage &&
chmod -R 775 /var/www/storage
'
```

> Para persistir no host, mapeie um volume no `docker-compose.yml`:
> ```yaml
>   volumes:
>     - ./storage:/var/www/storage
> ```

---

## Variáveis/Config
- `inc/config.php` já lê as variáveis de ambiente e expõe `OPENAI_API_KEY`:
  ```php
  return [
    'DB' => [
      'HOST' => getenv('DB_HOST') ?: 'db',
      'PORT' => (int)(getenv('DB_PORT') ?: 3306),
      'NAME' => getenv('DB_NAME') ?: 'projeto_rafaelnasser',
      'USER' => getenv('DB_USER') ?: 'tccuser',
      'PASS' => getenv('DB_PASS') ?: 'senhaFort3!',
      'CHARSET' => getenv('DB_CHARSET') ?: 'utf8mb4',
    ],
  ];
  if (!defined('OPENAI_API_KEY')) {
    define('OPENAI_API_KEY', getenv('OPENAI_API_KEY') ?: '');
  }
  ```
- Para usar a **Análise da IA**, defina `OPENAI_API_KEY` no `.env`.

