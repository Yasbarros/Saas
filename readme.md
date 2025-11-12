## Setup do Projeto Laravel com Sail, Multi-Tenant e Xdebug

### 1. Instalar Dependências

```bash
composer install
npm install
```

### 2. Subir os containers com o Laravel Sail

```bash
./vendor/bin/sail up -d
```

#### (Opcional) Criar um alias para o Sail

Adicione o seguinte alias ao seu arquivo de configuração do terminal (`~/.bashrc`, `~/.zshrc`, etc.) para facilitar o uso do Sail:

```bash
alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'
```

Depois disso, reinicie o terminal ou execute `source ~/.bashrc` ou `source ~/.zshrc`.

Agora você pode usar comandos como:

```bash
sail up -d
sail stop
sail artisan migrate
sail composer run dev
```

> **Nota:** Apenas `sail up` roda o build de produção. Use `sail composer run dev` para desenvolvimento.

---

### 3. Rodar as Migrations

```bash
sail artisan migrate
```

---

### 4. Criar um Tenant

```bash
sail artisan tinker
```

No Tinker:

```php
$tenant1 = App\Models\Tenant::create(['id' => 'teste']);
$tenant1->domains()->create(['domain' => 'teste.localhost']);
```

---

### 5. Rodar Migrations do Tenant

```bash
sail artisan tenants:migrate
```

Agora você pode acessar: **[http://teste.localhost](http://teste.localhost)**

> Certifique-se de que o domínio `teste.localhost` esteja resolvendo localmente. Você pode adicionar ao seu arquivo `/etc/hosts`:

```
127.0.0.1 teste.localhost
```

---

### 6. Configurar Debug com Xdebug no VS Code

1. Instale a extensão **PHP Debug** no VS Code.
2. Vá na aba "Run and Debug" (Ctrl + Shift + D).
3. Clique em "create a launch.json file" e selecione **PHP (Xdebug)**.
4. Substitua ou edite a configuração para incluir:

```json
{
    "name": "Listen for Xdebug",
    "type": "php",
    "request": "launch",
    "port": 9003,
    "pathMappings": {
        "/var/www/html": "${workspaceFolder}"
    },
    "hostname": "0.0.0.0"
}
```

> Se já existir um bloco `"Listen for Xdebug"`, certifique-se de que ele tenha os campos `pathMappings` e `hostname` como acima.

---

Pronto! Seu ambiente está configurado com Laravel Sail, suporte a multi-tenant e debug com Xdebug.
