# Token 系统

## 简介

Token系统为实现对用户的访问控制以及对API Request的真实性进行检验。

## 实现

在用户/客服/管理员登录后会获得相应Token，Token有效期为30分钟，Token更新时间记录在数据库。