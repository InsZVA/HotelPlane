## API文档说明

## 请求

全部采用POST请求，请发送JSON字符串(application/json),请勿发送(x-www-form-urlencoded)，json中请带上`userId`
和`token`字段用于访问控制，`requestMethod`方法用于表示所请求的api方法名。

## 参数说明

所有用到的时间相关的参数全部为时间戳。

## 响应XX

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
| newHotel | data | data中子参数有：name, address, [star], [remarks], images, cityId, countyId, phone, [type], [description], images为图片url数组，json字符串 | OK |
| listHotels | [offset] [num] [orderBy] [order] | order = asc \| desc | OK |
| findHotelsByCity | [offset] [num] cityId [orderBy] [order] | | OK |
| findHotelsByCounty | [offset] [num] countyId [orderBy] [order] | | OK |
| editHotel | hotelId, data | data中子参数与创建时相同 | OK |
| deleteHotel | hotelId | 会同时删掉所有房型 | OK |
| getRooms | hotelId | | OK |
| newRoom | hotelId data | data中子参数有：name, image, description, price | OK |
| deleteRoom | hotelId roomId | | OK |
| getHotelData | hotelId | | TEST |
| 地址相关 | | | |
| getCities | chinese | 0表示国际 1表示国内 | OK |
| newCity | data | data中子参数有：name, chinese, letter(首字母) | OK |
| deleteCity | cityId | 必须先删除该城市下所有旅馆机票和区才能删除该城市 | OK |
| getCounties | cityId | | OK |
| newCounty | data, cityId | data中子参数有：name, letter | OK |
| deleteCounty | cityId countyId | | OK |
| getCityData | cityId | | OK |
| getCity | countyId | | OK |
| 用户相关 | | | |
| newUser | data | data中子参数有：username, password, level, [inviterId] | OK |
| login | data | data中子参数有：username, password | OK |
| getAvatar | | | OK |
| setAvatar | avatar | | OK |
| getAddress | | | OK |
| setAddress | data | data中子参数为: cityId, countyId | OK |
| isVerified | | | OK |
| verify | data | data中子参数为：realname, phone | OK |
| getID | | id_type: 0为身份证 1为护照 | OK |
| setID | data | data中子参数为：idType, idCode | OK |
| bindOpenId | openId | | OK |
| changePassword | data | data中子参数为：oldPassword, newPassword | OK |
| listUsers | [offset] [num] [orderBy] [order] | order = asc \| desc | OK |
| findUserByPhone | phone | | OK |
| findUserByIdCode | idCode idType | | OK |
| getUserData | | | OK |
| 优惠券相关 | | | |
| newCoupon | data | data中子参数为：discount, minPrice, [startTime], endTime, type(0 新用户注册获得 1 老用户分享获得 -1 已下架) | OK |
| getUserCoupons | userId | | OK |
| offCoupon | couponId | | OK |
| getCoupons | | | OK |
| 活动相关 | | | |
| newActivity | data | data中子参数为：name, description, image, oldPrice, price | OK |
| editActivity | activityId data | | OK |
| offActivity | activityId | | OK |
| setActivityWeight | activityId weight | | OK |
| listAvailableActivity | | | OK |
| 机票相关 | | | |
| newPlane | data | data中子参数为：flight_number(航班号),start_city_id,start_airport,end_city_id,end_airport,start_time,end_time,remarks,standard(1为非标,0为标准),type(该字段代表舱型),price | OK |
| deletePlane | planeId | | OK |
| editPlane | data | data子参数比newPlane多一个plane_id | OK |
| listPlanes | offset,num,orderBy,order,standard | orderBy可选参数为start_city_id,start_time,order(asc或者desc),standard(0或1)| OK |
| search | keyword, offset,num | | OK |
| findPlanes | data | data子参数为start_city_id,end_city_id,start_date,offset,num| OK |
