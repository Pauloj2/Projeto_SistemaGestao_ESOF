from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.alert import Alert
import time

# Caminho do ChromeDriver
servico = Service("C:/chromedriver-win64/chromedriver-win64/chromedriver.exe")
driver = webdriver.Chrome(service=servico)
driver.maximize_window()

# Função para abrir o modal de novo cliente
def abrir_modal_cadastro():
    driver.get("http://localhost/sistema_esofOrganizado/controllers/clientes.php")
    time.sleep(2)
    botao_novo_cliente = driver.find_element(By.XPATH, "//a[contains(text(),'+ Novo Cliente')]")
    botao_novo_cliente.click()
    time.sleep(1)

# ✅ TESTE 1: Cadastro válido
abrir_modal_cadastro()
driver.find_element(By.NAME, "txtnome").send_keys("Maria Teste")
driver.find_element(By.NAME, "txttelefone").send_keys("(34) 99999-8888")
driver.find_element(By.NAME, "txtendereco").send_keys("Rua Exemplo, 123")
driver.find_element(By.NAME, "txtemail").send_keys("maria@teste.com")
driver.find_element(By.NAME, "txtcpf").send_keys("123.456.789-00")
driver.find_element(By.NAME, "button").click()
time.sleep(2)

try:
    alerta = Alert(driver)
    mensagem = alerta.text
    print(f"🔔 Teste 1 Alerta: {mensagem}")
    if "Sucesso" in mensagem:
        print("✅ Teste 1 OK: Cliente cadastrado com sucesso.")
    else:
        print("⚠️ Teste 1: Alerta inesperado.")
    alerta.accept()
except:
    print("❌ Teste 1 FALHOU: Nenhum alerta exibido.")

# ❌ TESTE 2: Cadastro inválido (CPF em branco)
abrir_modal_cadastro()
driver.find_element(By.NAME, "txtnome").send_keys("Teste Inválido")
driver.find_element(By.NAME, "txttelefone").send_keys("(34) 98888-0000")
driver.find_element(By.NAME, "txtendereco").send_keys("Rua B, 200")
driver.find_element(By.NAME, "txtemail").send_keys("invalido@email.com")
# CPF deixado em branco propositalmente
driver.find_element(By.NAME, "button").click()
time.sleep(2)

try:
    alerta = Alert(driver)
    mensagem = alerta.text
    print(f"🟠 Teste 2 Alerta: {mensagem}")
    if "erro" in mensagem.lower() or "inválido" in mensagem.lower():
        print("✅ Teste 2 OK: Erro detectado como esperado.")
    else:
        print("⚠️ Teste 2 ALERTA inesperado.")
    alerta.accept()
except:
    print("✅ Teste 2 OK: O formulário foi bloqueado (sem CPF).")

# Finaliza o navegador
driver.quit()
