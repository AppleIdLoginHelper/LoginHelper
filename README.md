## 这是什么
众所周知，最近苹果又整幺蛾子了，提供的 `apple id` 稍不留神就会被永远不看注意事项的睿智用户开启两步认证，当你费劲千辛万苦关闭之后，又会被另一个睿智用户开启。宝贵的时间怎么能花在这种重复的事情上呢？

![https://inews.gtimg.com/newsapp_bt/0/12544143696/641](https://inews.gtimg.com/newsapp_bt/0/12544143696/641)

所以说，要用魔法打败魔法

## 效果演示
![https://github.com/AppleIdLoginHelper/AppleIdLoginHelper/blob/master/img/Snipaste_2022-01-30_01-42-05.png](https://github.com/AppleIdLoginHelper/AppleIdLoginHelper/blob/master/img/Snipaste_2022-01-30_01-42-05.png)

## 流程简要说明
- 注册或购买一个 `apple id` 账户（广告位招租）
- 购买软件礼品卡喂给 `apple id` （广告位招租）
- 注册或购买一个 `google voice` 账户（广告位招租）
- 在 [appleid.apple.com](https://appleid.apple.com) 登录你的 `apple id` 开启两步认证
- 绑定手机号时，使用你 `google voice` 账户持有的号码
- 参考文档，将此项目部署完成，然后将网站链接发给用户
- 用户登录时在网站获取验证码，便可完成登录

## 配置账户
- 使用你的 `google voice` 账户登录 `GCP` 控制面板：[cloud.google.com](https://cloud.google.com)
- 登录后，在左侧侧边栏找到 **api和服务**，点击子项 **oauth同意屏幕**
- 点击右侧的 **创建项目** 按钮
- 项目名称和位置用默认填写的就行，然后点击创建
- 等待页面加载完成
- **User Type** 选外部
- **应用名称** 随便
- **用户支持地址邮件** 选你的账户
- **已获授权的网域** 填你的域名
- （如果你的域名是 `my.domain.com` 这种子域名，填 `domain.com` 就行）
- **开发者联系信息** 填你的账户
- 范围页面无需设置，直接点击 **保存并继续**
- 添加测试用户，输入你的 `google voice` 账户邮箱
- 点击凭据 -> 创建凭据 -> `oauth`
- 应用类型：**web应用**
- **授权js来源** 填写你的域名
- （如果你的域名是 `my.domain.com` 这种子域名，那就填 `my.domain.com`，**！和上面的填法不一样！**）
- **授权重定向url** 填写你的域名，并在末尾加上 `/callback`
- （如果你的域名是 `my.domain.com` 这种子域名，那就填 `my.domain.com/callback`，**！和上面的填法不一样！**）
- 等待创建完成，然后在弹出的对话框中，点击下载 `json` 文件
- 将文件重命名为 `credentials.json` 后上传到网站根目录
- 顶部搜索框搜索 `gmail api`
- 点击第一个搜索结果，点击 **启用**

## 配置网站
- 在你的域名控制台添加一个 `A` 记录指向服务器 `IP`
- 新建一个网站

#### 宝塔用户
- 网站目录加上 `/public`
- 伪静态选择 `thinkphp`

#### lnmp.org 用户
- 网站目录加上 `/public`
- 将 `include rewrite/none.conf;` 改成 `include rewrite/thinkphp.conf;`

进入网站根目录，然后移除防跨站
```
chattr -i .user.ini
chattr -i public/.user.ini
rm -rf .user.ini public/.user.ini
```
拉取代码
```
git clone https://github.com/AppleIdLoginHelper/LoginHelper .
```
安装 `composer` 命令（已安装可跳过）
```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '906a84df04cea2aa72f40b5f787e49f22d4c2f19492ac310e8cba5b96ac8b64115ac402c8cd292b8a03482574915d1a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
mv composer.phar /usr/local/bin/composer
```
安装依赖包
```
composer update
```
授权
```
chmod 755 -R *
chown www -R *
```
复制配置文件
```
cp .example.env .env
```
编辑配置文件
```
[DATABASE]
TYPE = mysql
HOSTNAME = 127.0.0.1
DATABASE = 你的数据库名称
USERNAME = 数据库用户
PASSWORD = 数据库用户密码
HOSTPORT = 3306
CHARSET = utf8
DEBUG = true
```
使用 `phpmyadmin` 或使用命令 `mysql` 导入数据库结构 
```
/database/verify.sql
```
进入控制器目录
```
cd app/controller
```
重命名文件
```
mv example.Index.php Index.php
```
编辑文件 `Index.php`
```
// 开了 oauth2.0 的 gmail 账户 也是接收其他 gmail 转发邮件的收件箱
$email_address = '';

// '你买的有google voice号的gmail账户' => '(对应下载的软件名称或其他备注) 绑定这个gv号的apple id账户',
        
$email_correspondence = [
    '' => '',
];
```
- `$email_address` 就是你的 `google voice` 账户
- `$email_correspondence` 是为了解决多个 `apple id` 共用一个面板设计的

#### 情境假设
- 你有两个 `apple id` ，一个可以下载应用 `app_a`，一个可以下载应用 `app_b`
- 你的 `google_voice_1` 账户持有的电话号码绑定了可以下载 `app_a` 的 `apple_id_a`
- 你的 `google_voice_2` 账户持有的电话号码绑定了可以下载 `app_b` 的 `apple_id_b`
- 你开启了 `oauth2.0` 的 `google voice` 账户是 `google_voice_1`
- 修改 `google_voice_2` 账户的设置，将邮件转发到 `google_voice_1` 账户
- 那么你应该这么配置上面的代码

```
$email_address = '<google_voice_1>';
$email_correspondence = [
    '<google_voice_1>' => '(app_a) <apple_id_a>',
    '<google_voice_2>' => '(app_b) <apple_id_b>',
];
```
如果你只有一个 `apple id`
```
$email_address = '<google_voice_1>';
$email_correspondence = [
    '<google_voice_1>' => '(app_a) <apple_id_a>',
];
```
在网站目录下执行
```
php quickstart.php
```
- 复制生成的链接，在浏览器中粘贴访问
- 选择当前登录的账户
- 会提示 **此应用未经 Google 验证**
- 点左边的继续（不是那个大的蓝色按钮）
- 授予 `gmail` 应用访问权限
- 会重定向到你的网站，根据页面提示操作

然后正常情况下应该是返回

```
Labels:
- CHAT
- SENT
- INBOX
- IMPORTANT
- TRASH
- DRAFT
- SPAM
- CATEGORY_FORUMS
- CATEGORY_UPDATES
- CATEGORY_PERSONAL
- CATEGORY_PROMOTIONS
- CATEGORY_SOCIAL
- STARRED
- UNREAD
```

大功告成！访问 https://your.domain.com/test 浏览效果吧！

## 故障排查

#### 未定义数组索引: messages
收件箱需要有包含关键词 `Apple Id` 的邮件。一般来说 `google voice` 收到的短信都会自动转发到 `gmail` 收件箱中

#### mkdir(): permission denied
进入网站根目录，执行
```
chmod 755 -R *
chown www -R *
```

## 后续建议

#### 关闭 debug 模式
虽然使用的是最新 `thinkphp` 版本，但是为了安全，仍建议关闭调试模式
- 进入网站根目录
- 编辑 `.env`
- 将 `APP_DEBUG = true` 改成 `APP_DEBUG = false`

#### 修改网站路径
默认路径是 `test` ，为了确保只有你和用户知道这个地址，建议修改
- 进入网站根目录
- 进入 `route` 目录
- 编辑 `app.php`
- 将第一个 `test` 改成其他字符串

例如，将
```
Route::get('/test', 'index/test');
```
改成
```
Route::get('/amdyes', 'index/test');
```
