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

def abrir_modal_usuario():
    driver.get("http://localhost/sistema_esofOrganizado/controllers/usuarios.php")
    time.sleep(2)
    botao_novo_usuario = driver.find_element(By.XPATH, "//a[contains(text(),'+ Novo Usuário')]")
    botao_novo_usuario.click()
    time.sleep(1)

def preencher_formulario_usuario(usuario, senha, funcionario_index):
    driver.find_element(By.NAME, "txtusuario").send_keys(usuario)
    driver.find_element(By.NAME, "txtsenha").send_keys(senha)
    
    funcionario_select = driver.find_element(By.NAME, "txtfuncionario")
    opcoes = funcionario_select.find_elements(By.TAG_NAME, "option")

    if len(opcoes) <= 1:
        print("❌ ERRO: Nenhum funcionário disponível para seleção!")
        driver.quit()
        return False

    if funcionario_index >= len(opcoes):
        print("❌ ERRO: Índice de funcionário fora do alcance.")
        driver.quit()
        return False

    opcoes[funcionario_index].click()
    driver.find_element(By.NAME, "button").click()
    time.sleep(2)
    return True

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

# 1. Login como administrador
fazer_login("admin", "123")  # Substitua por credenciais válidas

# 2. TESTE 1 – Cadastro de novo usuário
abrir_modal_usuario()
if preencher_formulario_usuario("pauloj", "098123", 5):
    validar_alerta("1", "cadastrado com sucesso")

# 3. TESTE 2 – Tentativa de duplicar usuário
abrir_modal_usuario()
if preencher_formulario_usuario("pauloj", "senha123", 1):
    validar_alerta("2", "Usuário já cadastrado")

# 4. Finaliza o navegador
driver.quit()
