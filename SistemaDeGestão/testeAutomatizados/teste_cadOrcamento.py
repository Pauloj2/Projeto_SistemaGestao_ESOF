from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.alert import Alert
import time

# Caminho do ChromeDriver
servico = Service("C:/chromedriver-win64/chromedriver-win64/chromedriver.exe")
driver = webdriver.Chrome(service=servico)
driver.maximize_window()

# ------------------ FUNÇÕES AUXILIARES ------------------

# Faz login no sistema
def fazer_login(usuario, senha):
    driver.get("http://localhost/sistema_esofOrganizado/index.php")
    time.sleep(1)
    driver.find_element(By.NAME, "usuario").send_keys(usuario)
    driver.find_element(By.NAME, "senha").send_keys(senha)
    driver.find_element(By.XPATH, "//button[@type='submit']").click()
    time.sleep(2)

# Abre o modal de novo orçamento
def abrir_modal_orcamento():
    driver.get("http://localhost/sistema_esofOrganizado/controllers/abrir_orcamentos.php")
    time.sleep(2)
    botao_novo_orcamento = driver.find_element(By.XPATH, "//a[contains(text(),'+ Inserir Novo')]")
    botao_novo_orcamento.click()
    time.sleep(1)

# Preenche e envia o formulário de orçamento
def preencher_orcamento(cpf, tecnico_index, produto, serie, defeito, obs):
    driver.find_element(By.NAME, "txtcpf").send_keys(cpf)
    tecnico_select = driver.find_element(By.NAME, "funcionario")
    tecnico_options = tecnico_select.find_elements(By.TAG_NAME, "option")
    tecnico_options[tecnico_index].click()
    driver.find_element(By.NAME, "txtproduto").send_keys(produto)
    driver.find_element(By.NAME, "txtserie").send_keys(serie)
    driver.find_element(By.NAME, "txtdefeito").send_keys(defeito)
    driver.find_element(By.NAME, "txtobs").send_keys(obs)
    driver.find_element(By.NAME, "button").click()
    time.sleep(2)

# Valida e imprime o alerta exibido
def validar_alerta(teste_id, esperado):
    try:
        alerta = Alert(driver)
        mensagem = alerta.text
        print(f"🔔 Teste {teste_id} Alerta: {mensagem}")
        if esperado.lower() in mensagem.lower():
            print(f"✅ Teste {teste_id} OK: Mensagem esperada detectada.")
        else:
            print(f"⚠️ Teste {teste_id}: Mensagem inesperada.")
        alerta.accept()
    except:
        print(f"❌ Teste {teste_id} FALHOU: Nenhum alerta exibido.")

# ------------------ EXECUÇÃO DOS TESTES ------------------

# 1. Login como usuário válido (funcionário ou admin)
fazer_login("admin", "123")  # Substitua por credenciais válidas do seu sistema

# 2. TESTE 1: Cliente EXISTENTE
abrir_modal_orcamento()
preencher_orcamento("123.456.789-00", 0, "Geladeira", "A123456", "Não liga", "Urgente")
validar_alerta("1", "Sucesso")

# 3. TESTE 2: Cliente INEXISTENTE
abrir_modal_orcamento()
preencher_orcamento("000.000.000-00", 0, "TV", "X999999", "Tela quebrada", "Sem urgência")
validar_alerta("2", "não está cadastrado")

# 4. Finaliza o navegador
driver.quit()
