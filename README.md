
# 📊 Sistema de Gestão - EletroService

Este é um sistema web desenvolvido para o gerenciamento de clientes, orçamentos, funcionários, compras, pagamentos e movimentações financeiras de uma empresa de conserto de eletrodomésticos.

## 🛠️ Funcionalidades Principais

- **Login com controle de acesso** (Administrador, Funcionário, etc.)
- **Cadastro e gerenciamento de clientes**
- **Abertura, consulta e fechamento de orçamentos**
- **Controle de compras e gastos**
- **Painel do administrador e painel de funcionário**
- **Controle de funcionários e cargos**
- **Histórico de movimentações financeiras**
- **Logout seguro e controle de sessão**

## 📁 Estrutura do Projeto

```
SistemaDeGestao/
assets/
css/
img/
config/
conexao.php
controllers/
(lógica de negócio: orçamentos, clientes, usuários, etc)
views/
(interfaces: painéis, login, telas do sistema)
rel/
(relatórios)
vendor/
(bibliotecas de terceiros, ex: dompdf)
README.md
```
## ⚙️ Tecnologias Utilizadas

- **PHP** (backend)
- **MySQL** (banco de dados)
- **HTML5 & CSS3** (frontend)
- **Bootstrap** (estilização responsiva)
- **JavaScript / jQuery** (interatividade)
- **Dompdf** (geração de PDFs)
- **FontAwesome & Bootstrap Icons** (ícones)

## 🧪 Requisitos

- **Servidor Web**: Apache (XAMPP, WAMP ou similar)
- **PHP**: Versão 7.4 ou superior
- **MySQL**: Para o banco de dados

## ⚙️ Instalação

1. Clone ou extraia este repositório na pasta `htdocs` do XAMPP ou diretório correspondente:
   ```bash
   C:/xampp/htdocs/SistemaDeGestão
   ```

2. Crie o banco de dados MySQL e importe o script SQL (não incluso no ZIP).

3. Ajuste as configurações de conexão no arquivo `conexao.php` se necessário.

4. Acesse o sistema via navegador:
   ```bash
   http://localhost/SistemaDeGestão/index.php
   ```

## 👥 Perfis de Usuário

- **Administrador**: Acesso total ao sistema (painel administrativo, controle de usuários, movimentações etc.)
- **Funcionário**: Acesso limitado ao painel funcional (abertura de orçamentos, consulta de OS, etc.)

## 📌 Observações

- Certifique-se de que o banco de dados esteja corretamente configurado.
- As permissões de acesso são controladas via sessões PHP.
- O estilo da interface é feito com CSS puro (sem uso de frameworks como Bootstrap).

## 📃 Licença

Este projeto é de uso acadêmico. 
