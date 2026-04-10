# 🏛️ Portal da Transparência Municipal

Este é um sistema robusto de Transparência Pública desenvolvido para municípios, permitindo que cidadãos e órgãos de controle acompanhem em tempo real a gestão orçamentária, financeira, patrimonial e legislativa da administração pública.

O projeto foi construído seguindo as diretrizes da **Lei de Acesso à Informação (Lei nº 12.527/2011)** e da **Lei de Responsabilidade Fiscal**.

## 📋 Funcionalidades do Portal

### 1. Planejamento (LOA)
* Consulta detalhada da **Lei Orçamentária Anual**.
* Filtros de Despesas e Receitas por: Elemento, Órgão e Recurso.

### 2. Gestão de Despesas
* **Execução Financeira:** Empenho Orçamentário e Execução Orçamentária.
* **Detalhamento:** Consultas por Credor, Órgão, Recurso e Localizador.
* **Transferências e Repasses:** Controle de Diárias, Decretos, Repasses e Duodécimos.

### 3. Receita e Arrecadação
* Acompanhamento da Arrecadação Municipal por Elemento e Recurso.
* Execução Orçamentária das receitas previstas vs. realizadas.

### 4. Compras, Licitações e Contratos
* **Processos Licitatórios:** Editais, anexos e resultados de licitações.
* **Contratos:** Gestão de Contratos Administrativos.
* **Registro de Preços:** Atas e itens registrados.
* **Requisições:** Histórico de pedidos por fornecedor, solicitante ou elemento.

### 5. Gestão de Pessoal (RH)
* **Quadro Funcional:** Servidores listados por função, lotação e regime jurídico.
* **Folha de Pagamento:** Transparência salarial (Relação Nominal/Por Servidor).

### 6. Módulo Parlamentar (Câmara Municipal)
* **Legislatura:** Consulta de Parlamentares e composição da Mesa Diretora.
* **Atividade Legislativa:** Sessões plenárias, pautas e registros.

### 7. Patrimônio e Publicações
* **Patrimônio:** Inventário de bens móveis e imóveis do município.
* **Publicações:** Central de atos oficiais, decretos e prestações de conta.
* **Cronograma:** Acompanhamento de desembolso mensal/anual.

---

## 🛠️ Tecnologias Utilizadas

* **Framework:** [Laravel 10+](https://laravel.com/)
* **Linguagem:** PHP 8.1+
* **Frontend:** Bootstrap 5 (Componentes e Navegação) & Tailwind CSS (Estilização específica).
* **Ícones:** Font Awesome (Biblioteca de ícones administrativos).
* **Banco de Dados:** MySQL com consultas otimizadas para grandes volumes de dados legados.

## ⚙️ Características Técnicas

* **Arquitetura Multi-Client:** O sistema adapta automaticamente a identidade visual (Logotipos e cores) com base no cliente configurado no ambiente.
* **Navegação Inteligente:** Menu multinível com estados "active" baseados na rota atual.
* **Exportação de Dados:** Componentes prontos para exportação de tabelas em PDF e Excel.

## 🚀 Instalação e Setup

1.  **Clonar o repositório:**
    ```bash
    git clone [https://github.com/seu-usuario/portal-transparencia.git](https://github.com/seu-usuario/portal-transparencia.git)
    ```

2.  **Configurar o ambiente:**
    ```bash
    cp .env.example .env
    ```
    *Ajuste as credenciais de banco de dados e a chave `APP_CLIENT_NAME` para definir o município.*

3.  **Instalar dependências:**
    ```bash
    composer install
    npm install && npm run dev
    ```

4.  **Executar o servidor:**
    ```bash
    php artisan serve
    ```

---
*Desenvolvido por Hardsoft Sistemas.*