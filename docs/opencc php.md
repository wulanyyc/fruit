## CentOS

### 编译安装opencc
```shell
git clone git@github.com:BYVoid/OpenCC.git
cd OpenCC
yum install gettext-devel
git checkout ver.0.4.3
./release.sh
cd release
make install
```

### 编译安装php opencc扩展
```shell
git clone git@github.com:BYVoid/opencc-php.git
cd opencc-php
phpize
./configure
make
make install
```
>>> 请自行启用扩展


## Debian/Ubuntu

### 安装libopencc
```shell
sudo apt-get install libopencc-dev
```

### 编译安装php opencc扩展
>>> 同CentOS部分
启用扩展
```shell
echo "extension=opencc.so" | sudo tee /etc/php5/mods-available/opencc.ini
sudo php5enmod opencc
```
