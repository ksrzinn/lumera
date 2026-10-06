# Mapa de telas

Referência: `docs/telas/WhatsApp Image 2026-10-06 at 20.23.49.jpeg` (15 telas de app mobile, marca "Cuidar").
Layout em coluna única, estilo app, com barra inferior (Início, Exames, Chat, Perfil).

| # | Tela de referência | Rota | Componente Vue | Etapa |
|---|---|---|---|---|
| 1 | Splash / logo "Cuidar" | `/` | `Pages/Welcome.vue` | 1 |
| 2 | Bem-vinda! (login) | `/login` | `Pages/Auth/Login.vue` | 2 |
| 3 | Criar conta | `/register` | `Pages/Auth/Register.vue` | 2 |
| 4 | Escolha o seu acesso (paciente ou profissional) | `/register` (seletor de perfil) | `Pages/Auth/Register.vue` | 2 |
| 5 | Início (saudação, próximo exame, atalhos) | `/dashboard` | `Pages/Patient/Dashboard.vue` | 7 |
| 6 | Informações (lista de temas) | `/informacoes` | `Pages/Info/Index.vue` | 3 |
| 7 | Informação: O que é, Fatores de risco, Prevenção, HPV e vacinação, Sintomas, Exames | `/informacoes/{slug}` | `Pages/Info/Show.vue` | 3 |
| 8 | Saiba mais (Vacina contra HPV) | `/informacoes/hpv-e-vacinacao` | `Pages/Info/Show.vue` | 3 |
| 9 | Como você está se sentindo? (sintomas) | `/ciclo/novo` | `Pages/Cycle/Form.vue` | 5 |
| 10 | Questionário de saúde (passo a passo) | `/questionario` | `Pages/Questionnaire/Form.vue` | 4 |
| 11 | Meus exames (situação do preventivo) | `/exames` | `Pages/Exams/Index.vue` | 7 |
| 12 | Lembretes (filtros Todos, Exames, Consultas) | `/lembretes` | `Pages/Reminders/Index.vue` | 6 |
| 13 | Fale com o(a) médico(a) (chat) | `/mensagens` | `Pages/Messages/Show.vue` | 8 |
| 14 | Meu perfil | `/perfil` | `Pages/Profile/Edit.vue` | 7 |
| 15 | Serviços de saúde (UBS próximas) | `/servicos` | `Pages/Services/Index.vue` | 9 |
| 16 | Frase "A informação também é uma forma de cuidado" | Tela de abertura / rodapé | `Components/Quote.vue` (opcional) | 11 |

## Componentes compartilhados (Etapa 1)

| Componente | Uso |
|---|---|
| `Layouts/AppLayout.vue` | Cabeçalho, barra inferior, rodapé (área logada) |
| `Layouts/GuestLayout.vue` | Telas de login e cadastro |
| `Components/PrimaryButton.vue`, `SecondaryButton.vue` | Botão rosa cheio e botão contornado |
| `Components/Card.vue` | Cartão rosa claro, opcionalmente clicável com seta |
| `Components/TextInput.vue` | Campo de texto arredondado |
| `Components/Alert.vue` | Avisos (info, sucesso, atenção, erro) |
| `Components/Icon.vue` | Ícones de linha usados nas telas |
| `Components/BrandLogo.vue` | Marca "Cuidar" |

## Observações
- Itens da referência fora do escopo (CLAUDE.md): "Esqueci minha senha" não será implementado; "Entrar com CPF" não existe no modelo de dados (login por e-mail).
- "Vacinação HPV" e "Resultados" no perfil/exames dependem de dados que o modelo não tem; ficam como links para as páginas de informação.
