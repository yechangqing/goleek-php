create database goleek_test default character set utf8;
use goleek_test;

# 期货合约表
create table futures
(
	id int unsigned auto_increment,
	code varchar(50) unique not null comment '代号',
	name varchar(50) not null comment '名称',
	margin decimal(3,2) not null comment '保证金比例',
	unit int not null comment '每手单位',
	min decimal(9,2) not null comment '最小价格变动',
	exchange set('上海期货','大连商品','郑州商品') not null comment '交易所',
	interest set('y','n') not null default 'n' comment '是否是自选',

	primary key (id),
	check (margin > 0 and margin <= 1),
	check (unit >= 1),
	check (min > 0)
);

# 股票表
create table stock
(
	id int unsigned auto_increment,
	code varchar(50) unique not null comment '代号',
	name varchar(50) not null comment '名称',
	exchange set('上海证券','深圳证券') not null comment '交易所',
	interest set('y','n') not null default 'n' comment '是否是自选',

	primary key (id)
);

# 账户表
create table account
(
	id int unsigned auto_increment,
	code varchar(50) unique not null comment '账号',
	company varchar(50) not null comment '公司',
	money decimal(9,2) not null default 0 comment '权益',
	type set('期货','股票') not null comment '账户类型，外键', 
	used set('y','n') not null default 'n' comment '是否使用',

	primary key (id),
	check (money >= 0)
);

# 交易设置
create table setting
(
	id int unsigned auto_increment,
	open_percent decimal(3,2) not null default 0.25 comment '开仓比例',
	loss_percent decimal(4,3) not null default 0.02 comment '止损比例',

	primary key (id),
	check (open_percent > 0 && open_percent <= 1),
	check (loss_percent > 0 && loss_percent <= 1)
);

insert into setting (id, open_percent, loss_percent) values (1,0.25,0.02);

# 期货交易历史明细
create table detail_futures
(
	id int unsigned auto_increment,
	contract varchar(50) not null comment '合约代号',
	name varchar(50) not null comment '合约名称',
	status set('持','平') not null default '持' comment '未平仓还是已经平仓',
	direct set('多','空') not null comment '方向',
	open_price decimal(9,2) not null comment '开仓价格',
	open_date date not null comment '开仓日期',
	close_price decimal(9,2) default null comment '平仓价格',
	close_date date default null comment '平仓日期',
	margin decimal(3,2) not null comment '开仓当时的保证金',
	unit int not null comment '当时的每手单位',
	account varchar(50) not null comment '开仓的账户',

	primary key (id),
	check (open_price > 0),
	check (close_price > 0),
	check (margin > 0 and margin <= 1),
	check (unit > 0)
);

# 股票交易历史明细
create table detail_stock
(
	id int unsigned auto_increment,
	code varchar(50) not null comment '代号',
	name varchar(50) not null comment '名称',
	status set('持','平') not null default '持' comment '未平仓还是已经平仓',
	open_price decimal(9,2) not null comment '开仓价格',
	open_date date not null comment '开仓日期',
	close_price decimal(9,2) default null comment '平仓价格',
	close_date date default null comment '平仓日期',
	account varchar(50) not null comment '开仓的账户',
	
	primary key (id),
	check (open_price > 0),
	check (close_price > 0)
);

# 期货持仓
create table position_futures
(
	id int unsigned auto_increment,
    quit_price decimal(9,2) default null comment '预计离场价格', 
    action set('卖出平仓 >=','卖出平仓 <=','买入平仓 <=','买入平仓 >=') not null comment '离场动作',

	primary key (id)
);

# 具体持仓的明细
create table position_detail_futures
(
	id int unsigned auto_increment,
	position_futures_id int unsigned not null comment '仓位id，外键',
	detail_futures_id int unsigned not null comment '明细id，外键',
	
	primary key (id),
	foreign key (position_futures_id) references position_futures (id) on delete cascade on update cascade,
	foreign key (detail_futures_id) references detail_futures (id) on delete cascade on update cascade
);

# 股票持仓
create table position_stock
(
	id int unsigned auto_increment,
	quit_price decimal(9,2) default null comment '预计离场价格', 
	action set('卖出 >=','卖出 <=') not null comment '离场动作',

	primary key (id)
);

# 具体持仓明细
create table position_detail_stock
(
	id int unsigned auto_increment,
	position_stock_id int unsigned not null comment '仓位id，外键',
	detail_stock_id int unsigned not null comment '明细id，外键',

	primary key (id),
	foreign key (position_stock_id) references position_stock (id) on delete cascade on update cascade,
	foreign key (detail_stock_id) references detail_stock (id) on delete cascade on update cascade
);

# 允许的客户端计算机列表
create table client
(
	id int unsigned auto_increment,
	mac_address varchar(50) unique not null comment 'mac地址列表',
	note varchar(100) default '' comment '说明',

	primary key (id)
);

insert into client (mac_address,note) values 
('80-56-F2-7D-86-04','笔记本无线网卡'),
('AO-D3-C1-72-75-74','笔记本有线网卡'),
('00-50-56-C0-00-01','笔记本虚拟网卡1'),
('00-50-56-C0-00-08','笔记本虚拟网卡2');


### 视图

# 期货持仓
create view v_position_futures as
select d.contract, d.direct, count(d.id) as lot, round(avg(d.open_price),2) as open_price,
	p.action, round(p.quit_price,2) as quit_price , d.account, min(d.open_date) as open_date , p.id
from position_detail_futures as pd
join position_futures as p on p.id=pd.position_futures_id
join detail_futures as d on d.id = pd.detail_futures_id
where d.status='持'
group by contract,direct,account
order by min(d.open_date), p.id
;

# 股票持仓
create view v_position_stock as
select d.code, d.name, count(d.id) as lot, round(avg(d.open_price),2) as open_price,
	p.action, round(p.quit_price,2) as quit_price , d.account, min(d.open_date) as open_date, p.id
from position_detail_stock as pd
join position_stock as p on p.id = pd.position_stock_id
join detail_stock as d on d.id = pd.detail_stock_id
where d.status='持'
group by account, code
order by min(d.open_date), p.id
;