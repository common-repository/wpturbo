=== WPTurbo -WordPress性能优化插件 ===
Contributors: wbolt,mrkwong
Donate link: https://www.wbolt.com/
Tags: speedup, cache, optimize, CDN, OSS, mysql, google font, preload, lazyload
Requires at least: 5.6
Tested up to: 6.6
Stable tag: 2.0.3
Requires PHP: 7.0
License: GNU General Public License v2.0 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WPTurbo如其名，即WordPress的涡轮增压器，是一款专门针对WordPress开发的性能优化插件，效用包括WP瘦身，WP速度优化，数据库优化及对象存储等。

== Description ==

WordPress性能影响因素有诸多，如主题和插件代码，服务器托管商，服务器，未优化的内容、外部HTTP请求过多及未能使用专用网络提供静态资源等。

当然也有非常多的方式来实现加速WordPress，比如参考<a href="https://www.wbolt.com/wordpress-slow.html#17-ways-to-fix-a-slow-wordpress-website"  rel="friend" title="WordPress加速教程的17种方法">我们的WordPress加速教程的17种方法</a>，<a href="https://www.wbolt.com/speed-up-wordpress.html" rel="friend" title="WordPress网站速度优化终极指南">WordPress网站速度优化终极指南</a>，及<a href="https://www.wbolt.com/google-pagespeed-insights-scoring-100.html" rel="friend" title="如何做到Google PageSpeed Insights测试满分">如何做到Google PageSpeed Insights测试满分</a>。

方法自然有不计其数，但万变不离其宗。并且对于大部分站长来说，有些方法看似简单，但实施起来，没有两把子，估计举步维艰。

为此，我们开发了这款WordPress性能优化插件-WPTurbo。该插件的功能模块主要包括WP瘦身、速度优化、数据库优化及对象存储。

### 1.WP瘦身

WP瘦身，即取其精华去其糟粕，尽可能将我们没有用到的东西干掉及对部分代码、功能进行优化。包括：

* **禁用**-支持对Emojis、前端Admin栏、嵌入、XML-RPC、RSS源及链接、Self-pingbacks、REST API和评论等禁用；
* **移除**-支持移除jquery migrate、WP版本信息、wlwmanifest链接、RSD链接、短链、REST API链接和评论链接；
* **优化**-支持限制修订历史，修改自动保存频率和Heartbeat频率，及优化Gravatar加载；
* **谷歌字体**-支持禁用谷歌字体、设置Swap字体显示属性、本地化谷歌字体、CDN缓存谷歌字体及清除本地字体等。

> ℹ️ <strong>Tips</strong> 
> 
> 1.对症下药才是根本，优化前先了解<a href="https://www.wbolt.com/wordpress-slow.html?utm_source=wp&utm_medium=link&utm_campaign=wpturbo" rel="friend" title="WordPress慢的原因">问题所在</a>。
> 2.深度阅读谷歌的<a href="https://www.wbolt.com/core-web-vitals.html?utm_source=wp&utm_medium=link&utm_campaign=wpturbo" rel="friend" title="Core Web Vitals">Core Web Vitals标准</a>。
> 3.根据<a href="https://www.wbolt.com/speed-up-wordpress.html?utm_source=wp&utm_medium=link&utm_campaign=wpturbo" rel="friend" title="WordPress网站速度优化终极指南">WordPress网站速度优化终极指南</a>逐个击破。
> 4.学会利用<a href="https://www.wbolt.com/google-pagespeed-insights-scoring-100.html?utm_source=wp&utm_medium=link&utm_campaign=wpturbo" rel="friend" title="Google PageSpeed Insights">Google PageSpeed Insights</a>发现和解决问题。

### 2.速度优化

天下武功唯快不破。插件提供多种WordPress速度优化的方式，包括CDN、预加载、懒加载、自定义代码及JS管理等。

* **CDN管理**-启用CDN网络，以多点分发网站静态资源，提升网站加载速度。
* **预加载**-通过DNS预取、预连接及预加载关键词和静态资源等方式，能够改善网站的用户浏览体验。
* **懒加载**-对WordPress网站页面的图像添加懒加载属性，可以大幅度提升网站首次加载的速度，提升网站用户体验。
* **JS管理**-允许管理页面加载JS脚本，JS加载方式及页面自定义JS等。（暂不提供管理页面JS脚本支持）

> ℹ️ <strong>Tips</strong> 
> 
> 1.如属通过CDN和对象存储联合提供网站静态资源访问支持，务必查看<a href="https://www.wbolt.com/aliyun-oss.html?utm_source=wp&utm_medium=link&utm_campaign=wpturbo" rel="friend" title="CDN配置">配置教程</a>。
> 2.如果仅需要对媒体库图片进行CDN加速，包含目录设置为 /wp-content/uploads。
> 3.部分主题或者插件的静态资源如通过CDN网络读取可能会出现异常，则需要在排除列表设置为例外。

### 3.数据库优化

启用数据库优化模块，对数据库进行适当的清理、优化有利于提升数据库性能。

* **修订版本**-每当您保存草稿或更新已发布的WordPress页面或文章时，内容管理系统 (CMS) 都会自动创建修订。您运行站点的时间越长，您可能在数据库中存储的修订就越多。
* **自动草稿**-在您编辑文章时，WordPress会自动保存您的文章内容。这称为自动草稿。就像修订一样，这些会随着时间的推移在您的数据库中造成积累，导致资源浪费。
* **回收站文章**-回收站文章，如果没有及时清理，会随着网站的发展积累越来越多。在一定程度上会影响性能。建议及时清理不必要的回收站文章。
* **垃圾评论**-对WordPress熟悉的朋友，相信都清楚垃圾评论的恐怖之处，只要你开启评论功能，它们总是无孔不入的。建议安装Akismet反垃圾平台插件，并及时清理垃圾评论。
* **回收站评论**-回收站评论，与回收站文章类似，用于暂时存放丢弃的评论留言。如未能及时清理，会随着时间迁移积累大量数据。
* **过期瞬态**-除非您使用对象缓存，否则WordPress会在wp_options表中存储临时记录。通常这些都有一个过期时间，应该会随着时间的推移而消失。然而，情况并非总是如此。我们已经看到一些数据库中有数千条旧的临时记录。
* **所有瞬态**-开启此项会连同最新的临时记录一起清除。一般不建议开启此项，以免误杀一些当前需要的临时记录。
* **数据库表**-MySQL有一个OPTIMIZE TABLE命令，可用于回收MySQL安装中未使用的空间。这类似于硬盘碎片整理。注：该选项仅适用于MyISAM表，不适用于InnoDB表。
* **定期或立即优化**-如果您是一位懒人站长，可以根据网站的实际情况，设置定期优化数据库。插件将会根据上面的设置项，定期执行任务。注：默认为当前时区的凌晨3点钟执行。当然您也可以选择按照上方的设置参数，立即优化。

> ℹ️ <strong>Tips</strong> 
> 
> 1.数据库优化操作为不可逆，优化前请提前<a href="https://www.wbolt.com/14-best-wordpress-database-plugins.html?utm_source=wp&utm_medium=link&utm_campaign=wpturbo" rel="friend" title="数据库备份">备份数据库</a>。
> 2.除了上述数据库优化选项，您还可以选择<a href="https://www.wbolt.com/best-wordpress-database-optimization-plugins.html?utm_source=wp&utm_medium=link&utm_campaign=wpturbo" rel="friend" title="数据库备份">其他数据库优化插件</a>进一步优化。
> 3.如果您使用宝塔管理服务器，建议设置<a href="https://www.wbolt.com/bt-panel-task-management.html?utm_source=wp&utm_medium=link&utm_campaign=wpturbo" rel="friend" title="宝塔计划任务">计划任务</a>定期备份数据。

### 4.对象存储

启用对象存储，可以将指定目录内容同步至对象存储服务器，以减少主服务器的数据访问以提升服务器性能。

* **配置存储**--支持选择将对象存储配置保存至wp-config和数据库两种方式，并且可选对象存储服务器作为备份服务器或者文件读取服务器。
* **服务商**--支持阿里云、腾讯云、华为云和百度云等国内主流对象存储服务商的API配置；
* **服务模式**--支持按备份、访问服务（保留原文件）和访问服务（删除本地文件）三种模式使用对象存储服务。

> ℹ️ <strong>Tips</strong> 
> 
> 1.对象存储默认指定同步WordPress站点的wp-content目录下的uploads文件夹。
> 2.服务模式选择文件备份时，建议对象存储设置为私有策略；选择访问服务，则应该设置为公共读策略。
> 3.选择访问服务（镜像），指网站服务器和对象存储服务器均保存文件；访问服务（迁移），则网站服务器不保存原文件。
> 4.进一步了解对象存储设置，请参考<a href="https://www.wbolt.com/aliyun-oss.html?utm_source=wp&utm_medium=link&utm_campaign=wpturbo" rel="friend" title="OSS配置教程">配置教程</a>。 

== 其他WP插件 ==

WPTurbo是一款专门为WordPress开发的<a href="https://www.wbolt.com/plugins/wpturbo?utm_source=wp&utm_medium=link&utm_campaign=wpturbo" rel="friend" title="速度优化插件">速度优化插件</a>. 插件支持WP瘦身、速度优化、数据库优化和对象存储等多种方式，来实现对WordPress全面优化，以提升WordPress效率。

闪电博（<a href='https://www.wbolt.com/?utm_source=wp&utm_medium=link&utm_campaign=wpturbo' rel='friend' title='闪电博官网'>wbolt.com</a>）专注于原创<a href='https://www.wbolt.com/themes' rel='friend' title='WordPress主题'>WordPress主题</a>和<a href='https://www.wbolt.com/plugins' rel='friend' title='WordPress插件'>WordPress插件</a>开发，为中文博客提供更多优质和符合国内需求的主题和插件。此外我们也会分享WordPress相关技巧和教程。

除了付费内容插件插件外，目前我们还开发了以下WordPress插件：

- [多合一搜索自动推送管理插件-历史下载安装数190,000+](https://wordpress.org/plugins/baidu-submit-link/)
- [热门关键词推荐插件-最佳关键词布局插件](https://wordpress.org/plugins/smart-keywords-tool/)
- [Smart SEO Tool-高效便捷的WP搜索引擎优化插件](https://wordpress.org/plugins/smart-seo-tool/)
- [Spider Analyser – WordPress搜索引擎蜘蛛分析插件](https://wordpress.org/plugins/spider-analyser/)
- [IMGspider-轻量外链图片采集插件](https://wordpress.org/plugins/imgspider/)
- [MagicPost – WordPress文章管理功能增强插件](https://wordpress.org/plugins/magicpost/)
- [Online Contact Widget-多合一在线客服插件](https://wordpress.org/plugins/online-contact-widget/)
- [WP VK-付费内容管理插件](https://wordpress.org/plugins/wp-vk/)
- 更多主题和插件，请访问<a href="https://www.wbolt.com/?utm_source=wp&utm_medium=link&utm_campaign=wpturbo" rel="friend" title="闪电博官网">wbolt.com</a>!

如果你在WordPress主题和插件上有更多的需求，也希望您可以向我们提出意见建议，我们将会记录下来并根据实际情况，推出更多符合大家需求的主题和插件。

== WordPress资源 == 

由于我们是WordPress重度爱好者，在WordPress主题插件开发之余，我们还独立开发了一系列的在线工具及分享大量的WordPress教程，供国内的WordPress粉丝和站长使用和学习，其中包括：

**<a href="https://www.wbolt.com/learn?utm_source=wp&utm_medium=link&utm_campaign=wpturbo" target="_blank">1. Wordpress学院:</a>** 这里将整合全面的WordPress知识和教程，帮助您深入了解WordPress的方方面面，包括基础、开发、优化、电商及SEO等。WordPress大师之路，从这里开始。

**<a href="https://www.wbolt.com/tools/keyword-finder?utm_source=wp&utm_medium=link&utm_campaign=wpturbo" target="_blank">2. 关键词查找工具:</a>** 选择符合搜索用户需求的关键词进行内容编辑，更有机会获得更好的搜索引擎排名及自然流量。使用我们的关键词查找工具，以获取主流搜索引擎推荐关键词。

**<a href="https://www.wbolt.com/tools/wp-fixer?utm_source=wp&utm_medium=link&utm_campaign=wpturbo">3. WOrdPress错误查找:</a>** 我们搜集了大部分WordPress最为常见的错误及对应的解决方案。您只需要在下方输入所遭遇的错误关键词或错误码，即可找到对应的处理办法。

**<a href="https://www.wbolt.com/tools/seo-toolbox?utm_source=wp&utm_medium=link&utm_campaign=wpturbo">4. SEO工具箱:</a>** 收集整理国内外诸如链接建设、关键词研究、内容优化等不同类型的SEO工具。善用工具，往往可以达到事半功倍的效果。

**<a href="https://www.wbolt.com/tools/seo-topic?utm_source=wp&utm_medium=link&utm_campaign=wpturbo">5. SEO优化中心:</a>** 无论您是 SEO 初学者，还是想学习高级SEO 策略，这都是您的 SEO 知识中心。

**<a href="https://www.wbolt.com/tools/spider-tool?utm_source=wp&utm_medium=link&utm_campaign=wpturbo">6. 蜘蛛查询工具:</a>** 网站每日都可能会有大量的蜘蛛爬虫访问，或者搜索引擎爬虫，或者安全扫描，或者SEO检测……满目琳琅。借助我们的蜘蛛爬虫检测工具，让一切假蜘蛛爬虫无处遁形！

**<a href="https://www.wbolt.com/tools/wp-codex?utm_source=wp&utm_medium=link&utm_campaign=wpturbo">7. WP开发宝典:</a>** WordPress作为全球市场份额最大CMS，也为众多企业官网、个人博客及电商网站的首选。使用我们的开发宝典，快速了解其函数、过滤器及动作等作用和写法。

**<a href="https://www.wbolt.com/tools/robots-tester?utm_source=wp&utm_medium=link&utm_campaign=wpturbo">8. robots.txt测试工具:</a>** 标准规范的robots.txt能够正确指引搜索引擎蜘蛛爬取网站内容。反之，可能让蜘蛛晕头转向。借助我们的robots.txt检测工具，校正您所写的规则。

**<a href="https://www.wbolt.com/tools/theme-detector?utm_source=wp&utm_medium=link&utm_campaign=wpturbo">9. WordPress主题检测器:</a>** 有时候，看到一个您为之着迷的WordPress网站。甚是想知道它背后的主题。查看源代码定可以找到蛛丝马迹，又或者使用我们的小工具，一键查明。

== Installation ==

方式1：在线安装(推荐)
1. 进入WordPress仪表盘，点击`插件-安装插件`，关键词搜索`WPTurbo`，找搜索结果中找到`WPTurbo`插件，点击`现在安装`；
2. 安装完毕后，启用`WPTurbo`插件.
3. 通过`WPTurbo`进行插件各项设置。 

方式2：上传安装

FTP上传安装
1. 解压插件压缩包`wpturbo.zip`，将解压获得文件夹上传至wordpress安装目录下的 `/wp-content/plugins/` 目录.
2. 访问WordPress仪表盘，进入“插件”-“已安装插件”，在插件列表中找到“WPTurbo”插件，点击“启用”.
3. 通过`WPTurbo`进行插件各项设置。 

仪表盘上传安装
1. 进入WordPress仪表盘，点击`插件-安装插件`；
2. 点击界面左上方的`上传按钮`，选择本地提前下载好的插件压缩包`wpturbo.zip`，点击`现在安装`；
3. 安装完毕后，启用`WPTurbo`插件；
4. 通过`WPTurbo`进行插件各项设置。 

关于本插件，你可以通过阅读<a href="https://www.wbolt.com/wpturbo-documentation.html?utm_source=wp&utm_medium=link&utm_campaign=wpturbo" rel="friend" title="插件教程">付费内容插件插件教程</a>学习了解插件安装、设置等详细内容。

== Frequently Asked Questions ==

暂无。

== Screenshots ==

1. WPTurbo-WP瘦身功能界面截图.
2. WPTurbo-速度优化功能界面截图.
3. WPTurbo-数据库优化功能界面.
4. WPTurbo-对象存储功能界面.

== Changelog ==

= 2.0.3 =
* 修复切换域名插件无法使用问题。

= 2.0.2 =
* 基于编码规范进一步优化PHP代码；
* 优化PHP代码以提升性能；
* 优化PHP代码以增强代码安全性。

= 2.0.1 =
* 修复插件设置服务模式显示问题。
* 增加同步媒体库资源到OSS。

= 2.0.0 =
* 新增预加载支持；
* 新增懒加载支持；
* 新增JS管理支持；
* 新增华为云和百度云OSS支持；
* 优化数据库优化交互；
* 优化插件输入项提示文本，以便于站长理解；
* 优化对象存储配置API和文件服务配置交互逻辑及设置项说明；
* 其他已知问题解决和bug修复。

= 1.0.0 =
* 首个版本发布。