<?php
/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/6/29
 * Time: 21:35
 */

const HOTEL_HALF_STANDARD = 0;
const HOTEL_NOT_STANDARD = 1;

const REGION_PROVINCE = 10000;
const REGION_CITY = 100;
const REGION_COUNTY = 1;

const ORDER_LIST = ['price', 'name', 'address', 'star', 'remarks', 'country', 'region_code', 'type', 'hot', 'hotel_id'];
const COLUMN_LIST = ['name', 'address', 'star', 'remarks', 'images', 'country', 'region_code', 'type', 'description'];
const COLUMN_TYPE_LIST = ['s', 's', 'i', 's', 's', 'i', 'i', 'i', 's'];