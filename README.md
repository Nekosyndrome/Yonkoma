# Yonkoma

## 介紹

Yonkoma 是一款 [futaba](https://www.2chan.net/script/) 樣式匿名討論版。Yonkoma 的名稱來源是最開始在 Komica 實驗此程式的四格版。

請注意這個專案是個人專案，與 Komica 並沒有程式以外關聯，對管理相關的問題一概無法回答。

## 開發方向與大綱

主要的修改方向有下列幾點:

- 程式效率: 基本目標是能在 1G ~ 2G 的 VPS 上 host 20 萬 PV 以上的討論版
- 管理效率: 多版的架構與管理介面與實作安全性的功能
- mobile-friendly 與 SEO: 我們會打算維護 server-side 與 front-end rendering 兩種版本
- 前後端分離與 dev environment 的開發

## 參與開發

**目前專案架構變更很大，建議先和作者討論開發方向再決定**

- 專案為 UNLICENSE 放棄著作權，請先確認是否有辦法接受在此前提下開發
- 請在 `develop` branch 上開發
- 請 commit unix-style 的格式(換行字元為\n)
- 後端開發:
  - coding style 請參考 [PSR-2](https://www.php-fig.org/psr/psr-2/)
  - 建議先從 docker 的測試環境開始開發
- 前端開發:
  - 待補

## 一些資料

- [API格式](https://github.com/Nekosyndrome/yonkoma/wiki/Api)
- 資料表格式 - pending

