from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time

# Caminho correto para o ChromeDriver
servico = Service("C:/chromedriver-win64/chromedriver-win64/chromedriver.exe")
driver = webdriver.Chrome(service=servico)
driver.maximize_window()

# Lista de testes (cada entrada representa uma linha da tabela)
testes = [
    {"usuario": "admin", "senha": "123", "esperado": "painel"},       # T1
    {"usuario": "admin", "senha": "senhaerrada", "esperado": "erro"}, # T2
    {"usuario": "joao123", "senha": "123", "esperado": "erro"},       # T3
    {"usuario": "", "senha": "123", "esperado": "erro"},              # T4
    {"usuario": "admin", "senha": "", "esperado": "erro"},            # T5
]

# Testes automatizados
for i, teste in enumerate(testes, start=1):
    print(f"Executando Teste T{i}...")

    driver.get("http://localhost/sistema_esof/index.php")
    time.sleep(1)

    campo_usuario = driver.find_element(By.NAME, "usuario")
    campo_senha = driver.find_element(By.NAME, "senha")

    campo_usuario.clear()
    campo_senha.clear()

    campo_usuario.send_keys(teste["usuario"])
    campo_senha.send_keys(teste["senha"] + Keys.RETURN)

    time.sleep(2)

    try:
        if teste["esperado"] == "painel":
            WebDriverWait(driver, 5).until(
                EC.visibility_of_element_located((By.CLASS_NAME, "menu-lateral"))
            )
            print(f"✅ T{i} OK: Login bem-sucedido (painel aberto)")
        else:
            WebDriverWait(driver, 5).until(
                EC.presence_of_element_located((By.XPATH, "//*[contains(text(),'Erro')]"))
            )
            print(f"✅ T{i} OK: Erro no login detectado")
    except:
        print(f"❌ T{i} FALHA: Resultado esperado devido a dados incorretos no login")

    time.sleep(1)

driver.quit()