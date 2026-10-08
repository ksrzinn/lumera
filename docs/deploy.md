# Deploy em servidor compartilhado (sem domínio, por IP)

O Cuidar roda com `docker-compose.prod.yml` e publica **uma única porta** (`CUIDAR_PORT`, padrão `8090`). O banco não
expõe porta. Nada nos outros projetos do servidor é alterado.

> **Sem HTTPS.** Acessando por `http://IP:PORTA`, o navegador trata a página como não segura. Consequências:
> - a geolocalização de `/servicos` não funciona (o app mostra o aviso e o campo de cidade continua valendo);
> - senhas e dados trafegam sem criptografia. Use apenas dados fictícios e uma senha que você não usa em nenhum outro lugar.
>
> Para HTTPS é preciso um domínio (por exemplo, um subdomínio gratuito do DuckDNS) e um proxy com certificado.

## 1. Conferir a porta

```bash
sudo ss -ltn | grep -w 8090 || echo "8090 livre"
```

Se estiver ocupada, escolha outra e use no passo 3. Se o provedor tem firewall no painel (a Contabo tem), libere a porta TCP escolhida.

## 2. Baixar o código

```bash
cd /var/www/html
git clone https://github.com/ksrzinn/lumera.git cuidar
cd cuidar
# o dono dos arquivos vai mudar no passo 3; sem isto, o git (como root) recusa o diretório
git config --global --add safe.directory /var/www/html/cuidar
```

## 3. Criar o `.env` de produção

```bash
cp .env.example .env
DBPASS=$(openssl rand -hex 16)
IP=$(curl -s https://ifconfig.me)   # ou escreva o IP da VPS
sed -i \
  -e "s|^APP_ENV=.*|APP_ENV=production|" \
  -e "s|^APP_DEBUG=.*|APP_DEBUG=false|" \
  -e "s|^APP_URL=.*|APP_URL=http://$IP:8090|" \
  -e "s|^DB_PASSWORD=.*|DB_PASSWORD=$DBPASS|" .env
echo "CUIDAR_PORT=8090" >> .env
# o servidor usa root; os arquivos do projeto precisam pertencer ao usuário 1000 do container
chown -R 1000:1000 .
```

## 4. Dependências e build do front

```bash
docker compose -f docker-compose.prod.yml build
docker compose -f docker-compose.prod.yml run --rm app composer install --no-dev --optimize-autoloader
docker run --rm -v "$PWD":/app -w /app node:20-alpine sh -c "npm ci && npm run build"
chown -R 1000:1000 .
```

O build do front usa uma imagem Node temporária, então não é preciso instalar Node no servidor.

## 5. Subir

```bash
docker compose -f docker-compose.prod.yml up -d
docker compose -f docker-compose.prod.yml run --rm app php artisan key:generate --force
docker compose -f docker-compose.prod.yml exec app php artisan migrate --force
```

Abra `http://IP:8090`.

## 6. Dados de exemplo (opcional)

O repositório é público e o seeder contém a senha das contas demo (`minhasenha`). Por isso a recomendação é **não rodar
o seeder** e deixar quem for testar criar a própria conta em `/register`.

Para ter as contas demo mesmo assim, troque as senhas logo depois:

```bash
docker compose -f docker-compose.prod.yml exec app php artisan db:seed --force
docker compose -f docker-compose.prod.yml exec app php artisan tinker --execute='
foreach (["paciente@cuidar.test","profissional@cuidar.test"] as $e) { App\Models\User::where("email",$e)->first()->update(["password" => "NOVA-SENHA-AQUI"]); }'
```

## Atualizar depois de novas alterações

```bash
cd /var/www/html/cuidar
git pull
chown -R 1000:1000 .
docker compose -f docker-compose.prod.yml run --rm app composer install --no-dev --optimize-autoloader
docker run --rm -v "$PWD":/app -w /app node:20-alpine sh -c "npm ci && npm run build"
chown -R 1000:1000 .
docker compose -f docker-compose.prod.yml exec app php artisan migrate --force
```

## Parar ou remover

```bash
docker compose -f docker-compose.prod.yml down        # para, mantém os dados
docker compose -f docker-compose.prod.yml down -v     # para e apaga o banco
```

## Quando houver domínio e HTTPS

- Com um proxy na frente, o Laravel precisa confiar nele (`trustProxies` em `bootstrap/app.php`) e o proxy precisa enviar
  `X-Forwarded-Proto` e acrescentar o IP real ao `X-Forwarded-For`. Sem isso, o app gera links `http` e o limite de
  tentativas de login pode ser burlado com um cabeçalho forjado.
- Defina `APP_URL=https://dominio` e, se o proxy ficar na frente, deixe de publicar a porta do Cuidar no host.
