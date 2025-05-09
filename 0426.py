import random
from DrissionPage import ChromiumPage,ChromiumOptions
from DrissionPage.errors import ElementNotFoundError
import pandas as pd
import time



if __name__ == "__main__":


    file_path = "表格.xlsx"
    df = pd.read_excel(file_path,dtype=str, sheet_name="Sheet1")
    df['法定代表人证件号码'] = df['法定代表人证件号码'].astype(str)

    username = 'x10041'
    password = 'lixin6111.'

    opts = ChromiumOptions()    
    opts.set_argument('--start-maximized')  
    dp = ChromiumPage(opts)
    dp.set.window.maximized()

  
    dp.headers = {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
    }


    url = "http://192.168.1.144/" 
    dp.get(url)
    print(dp.tabs)
    dp.ele('xpath://*[@id="textfield"]').input(username)
    dp.ele('xpath://*[@id="textfield2"]').input(password)
    dp.ele('xpath://html/body/form/div[3]/button').click()

    
    time.sleep(4)

    dp.to_tab(dp.find_tabs(url="login_old"))

    print(dp.tabs)
    dp.ele('xpath://*[@id="planetmap"]/area[2]').click()
    time.sleep(4)

    dp.to_tab(dp.find_tabs(url="login_manager"))

    time.sleep(1)
    dp.ele('xpath://*[@id="menuItem_main_cx"]/table/tbody/tr/td[2]/table/tbody/tr/td[1]').click()
    time.sleep(1)
    dp.ele('xpath://*[@id="menuItem_104"]/table/tbody/tr/td[2]/table/tbody/tr/td[1]').click()
    time.sleep(3)


    print("开始逐行处理数据...")
    for index, row in df.iterrows():

        if not len(row["法定代表人证件号码"]) <=3:
            continue 
        delay = random.uniform(5, 10)
        time.sleep(delay)
        dp.to_tab(dp.find_tabs(url="login_manager"))

        dp.ele('xpath://*[@id="pn_tab0"]/table/tbody/tr/td/table/tbody/tr[1]/td[4]/input').input(row["统一社会信用代码"])
        dp.ele('xpath://*[@id="BtnOk"]').click()
        


        time.sleep(5)
        dp.ele('xpath://*[@id="jdcxPage"]/table[3]/tbody/tr[2]/td[4]/a').click()

 
        dp.to_tab(dp.find_tabs(url="enter"))
        time.sleep(1)
        dp.ele('xpath://html/body/table[2]/tbody/tr/td[1]').click()
        dp.ele('xpath://*[@id="tabFddbr"]').click()
        time.sleep(2)
        dp.ele('xpath://*[@id="pn_tabFddbr"]/table/tbody/tr[1]/td/table/tbody/tr[2]/td[4]/a').click(True)
        dp.ele('xpath://*[@id="pn_tabFddbr"]/table/tbody/tr[3]/td/table/tbody/tr[5]/td[2]/a').click(True)

        df.at[index, "法定代表人证件号码"] = dp.ele('xpath://*[@id="qy_sl_fddbr_xq_cerno_span"]').text
        df.at[index, "原联络员手机号码"] = dp.ele('xpath://*[@id="llrycwry_xq_mobtel_span"]').text

        dp.close_tabs()       

        df.to_excel(file_path, index=False, sheet_name="Sheet1")

        
    dp.quit()
