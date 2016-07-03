## API文档说明

## 请求

全部采用POST请求，请发送JSON字符串(application/json),请勿发送(x-www-form-urlencoded)，json中请带上`userId`
和`token`字段用于访问控制，`requestMethod`方法用于表示所请求的api方法名。

## 响应

有请求内容的成功时直接直接响应内容，失败返回
```
    {"code": -1, "msg": "fail"}
```
没有请求内容的成功时返回
```
    {"code": 0, "msg": "success"}
```

## API列表

| 方法名 | 参数列表 | 备注 | 测试状态 |
|---|---|---|---|
| 宾馆相关 | | | |
| newHotel | data | data中子参数有：name, address, [star], [remarks], images, [country], regionCode, [type], [description], images为图片url数组，json字符串 | OK |
| listHotels | [offset] [num] [orderBy] [order] | order = asc \| desc | OK |
| findHotels | [offset] [num] regionCode range | range为范围，10000为同省，100为同市，1为同区 | OK |
| editHotel | hotelId, data | data中子参数与创建时相同 | OK |
| deleteHotel | hotelId | 会同时删掉所有房型 | OK |
| getRooms | hotelId | | OK |
| newRoom | hotelId data | data中子参数有：name, image, description, price | OK |
| deleteRoom | hotelId roomId | | OK |
| 地址相关 | | | |
| getProvinces | | 得到全国省份列表 | OK |
| getCities | regionId | regionId必须是省级 | OK |
| getCounties | regionId | regionId必须是市级 | OK |
| getHotCities | | 得到热门城市列表 | OK |