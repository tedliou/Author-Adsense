# Author Adsense for WordPress
## 讓網站協作者也能賺取廣告收益

這個 WordPress 外掛能在不同作者的文章頁面中自動切換 Adsense 廣告，讓作者們能賺各自的廣告費。

### 使用方法
**第一步** 目前暫不打算上架 WordPress.org，所以請直接從 Github 下載壓縮檔。

![](https://i.imgur.com/fHi4vSP.png)

**第二步** 上傳 Author Adsense 外掛到你的網站。

**第三步** 啟用外掛。

**第四步** 進入設定 > Author Adsense，輸入一個網站的預設發布商編號與欄位代碼。

![](https://i.imgur.com/5y9jK1L.png)

**第五步** 讓協作者登入自己的個人資料頁面，填入自己的 Adsense 發布商編號與欄位代碼，Author Adsense 會在文章頁面優先顯示協作者的廣告。

![](https://i.imgur.com/uZeSr2P.png)

**第六步** 更新網站根目錄的 ads.txt 內容。

`google.com, 廣告發布商編號, DIRECT, f08c47fec0942fa0`

**第七步** 修改廣告程式碼為此短代碼：

`[author-adsense]`

### 注意事項
* 目前僅支援單一廣告單元，存有複數的相同廣告欄位會有什麼影響，請自行測試，本人不負責。
* 如果協作者沒設定廣告資訊，Author Adsense 會自動顯示網站預設的廣告。
* 如果連預設廣告都沒設定，那廣告不會顯示。
* 沒使用 `[author-adsense]` 來顯示的廣告不會受此外掛的影響，請放心服用。
