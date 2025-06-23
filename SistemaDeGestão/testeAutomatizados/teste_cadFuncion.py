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

def fazer_login(usuario, senha):
    driver.get("http://localhost/sistema_esofOrganizado/index.php")
    time.sleep(1)
    driver.find_element(By.NAME, "usuario").send_keys(usuario)
    driver.find_element(By.NAME, "senha").send_keys(senha)
    driver.find_element(By.XPATH, "//button[@type='submit']").click()
    time.sleep(2)

def abrir_modal_funcionario():
    driver.get("http://localhost/sistema_esofOrganizado/controllers/funcionarios.php")
    time.sleep(2)
    botao_novo_func = driver.find_element(By.XPATH, "//a[contains(text(),'+ Novo Funcionario')]")
    botao_novo_func.click()
    time.sleep(1)

def preencher_formulario_funcionario(nome, cpf, telefone, endereco, cargo_index):
    driver.find_element(By.NAME, "txtnome").send_keys(nome)
    driver.find_element(By.NAME, "txtcpf").send_keys(cpf)
    driver.find_element(By.NAME, "txttelefone").send_keys(telefone)
    driver.find_element(By.NAME, "txtendereco").send_keys(endereco)
    cargo_select = driver.find_element(By.NAME, "txtcargo")
    cargos = cargo_select.find_elements(By.TAG_NAME, "option")
    cargos[cargo_index].click()  # exemplo: 1 = Funcionário
    driver.find_element(By.NAME, "button").click()
    time.sleep(2)

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

# 1. Login no sistema
fazer_login("admin", "123")  # Altere para usuário/senha válidos

# 2. TESTE 1 – Cadastro válido
abrir_modal_funcionario()
preencher_formulario_funcionario("João Silva", "222.333.444-55", "(34) 99999-1111", "Rua 1, Bairro A", 1)
validar_alerta("1", "Sucesso")

# 3. TESTE 2 – CPF já existente (erro esperado)
abrir_modal_funcionario()
preencher_formulario_funcionario("João Silva", "222.333.444-55", "(34) 99999-1111", "Rua 1, Bairro A", 1)
validar_alerta("2", "CPF já cadastrado")

# 4. Finaliza
driver.quit()
