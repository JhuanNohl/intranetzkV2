# IntranetZK

Base de aplicação interna construída com Laravel, Inertia.js e Vue 3. O objetivo do projeto é centralizar fluxos operacionais da empresa em uma intranet autenticada, com navegação única, módulos evolutivos e uma experiência de uso consistente para rotinas internas.

## Visão do projeto

O IntranetZK deve funcionar como uma central operacional para colaboradores. A primeira etapa estabelece a fundação técnica da aplicação: autenticação, layout principal, tema claro/escuro, dashboard inicial e rotas de módulos. A partir dessa base, os módulos reais podem ser desenvolvidos sem retornar ao modelo de páginas Blade tradicionais.

Módulos previstos nesta fase:

- Dashboard operacional.
- Chamados internos.
- Comunicados.
- Base de conhecimento.
- Colaboradores.

## Estado atual

A aplicação já está estruturada como uma base real Inertia:

- Root view Inertia em `resources/views/app.blade.php`.
- Middleware Inertia registrado no grupo `web`.
- Entrada Vue em `resources/js/app.js`.
- Layout principal em `resources/js/Layouts/AppShell.vue`.
- Dashboard vazio autenticado em `resources/js/Pages/Dashboard.vue`.
- Tela de login Inertia em `resources/js/Pages/Auth/Login.vue`.
- Compartilhamento global de `auth.user` via `HandleInertiaRequests`.
- Rotas protegidas por autenticação para dashboard e módulos.
- Tema claro/escuro controlado por `data-theme`.
- Projeto Vue separado removido; o frontend oficial passa a viver em `resources/js`.
- `welcome.blade.php` removido; Blade fica apenas como root técnico do Inertia.

## Relatório evolutivo

1. O projeto saiu do esqueleto padrão Laravel e passou a ter uma base Inertia + Vue funcional.
2. Foi criada a root view `app.blade.php`, responsável por carregar Vite, Inertia Head e o app Vue.
3. O middleware `HandleInertiaRequests` foi adicionado e configurado para compartilhar o usuário autenticado.
4. A rota `/` passou a renderizar o dashboard via Inertia e exigir autenticação.
5. Foram adicionadas rotas e páginas placeholder para os módulos principais.
6. Foi implementado login/logout usando autenticação web padrão do Laravel.
7. O layout principal passou a concentrar navegação, dados do usuário, alternância de tema e saída da sessão.
8. A documentação e os testes foram ajustados para refletir o novo fluxo autenticado.

## Stack

- Laravel 13.
- PHP 8.3+.
- Inertia Laravel.
- Vue 3.
- Vite.
- Tailwind CSS 4.
- SQLite em desenvolvimento local.

## Execução local

Instale dependências:

```bash
composer install
npm install
```

Prepare ambiente e banco:

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

Execute a aplicação:

```bash
php artisan serve
npm run dev
```

Credenciais locais criadas pelo seeder:

```text
E-mail: test@example.com
Senha: password
```

## Validação

Comandos usados para validar a base atual:

```bash
npm run build
php artisan test
php artisan route:list
```

No ambiente Windows atual, o binário correto de PHP identificado foi:

```text
C:\php\php.exe
```

Se `php` no terminal não responder corretamente, ajuste o `PATH` para priorizar esse binário.

## Próximos passos

- Definir modelo de permissões e perfis de usuário.
- Substituir placeholders por telas reais dos módulos.
- Criar componentes compartilhados de formulário, tabela, empty state e feedback.
- Implementar busca global quando houver dados reais.
- Evoluir testes de feature por módulo.
