<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 配置项
$API_KEY = "sk-你的deepseek  api";
$API_URL = "https://api.deepseek.com/chat/completions";
$result = "";
$topic = "";

if(isset($_POST['topic']))
{
    $topic = trim($_POST['topic']);
    // 优化后高阶Prompt（贴合红果短剧规则、话术、节奏、变现风格）
    $prompt = <<<PROMPT
# 身份定位
你是深耕红果短剧平台的金牌专职编剧，精通红果流量逻辑、用户喜好、叙事风格、台词风格、剪辑节奏，只产出**纯红果平台标准短剧文案**，拒绝文艺风、长篇文学、晦涩内容。

# 基础题材
{$topic}

# 强制输出结构（严格按顺序，使用标准Markdown，层级清晰）
1. **爆款剧名**：提供3个备选，风格抓人、吸睛、带情绪/冲突/逆袭感，适配短剧封面标题
2. **一句话爆款卖点**：20字以内，直击痛点、悬念拉满、突出爽点，适合封面简介
3. **世界观设定**：简短精炼，交代背景、势力、规则，不啰嗦
4. **主要人物设定**：区分主角、反派、配角，标注人设、身份、性格、对立关系
5. **前十集剧情大纲**（核心重点，红果标准格式）
   每一集固定包含三项：【剧情简述】【本集爽点】【结尾钩子】
   要求：
   - 节奏极快，开篇即冲突，每30秒一个小反转
   - 强对立、打脸、逆袭、打脸反派、扮猪吃虎、反转套路全覆盖
   - 每集结尾**必留强悬念**，引导用户看下一集
   - 台词口语化、接地气，符合下沉市场观看习惯
6. **爆款综合分析**：从题材热度、冲突密度、爽点频率、引流能力、用户留存5个维度打分+解析，判断能否成为平台爆款

# 硬性风格规则（必须遵守）
1. 整体：强冲突、高反转、全程高能、无水剧情、快节奏
2. 风格：标准红果短剧风格，拒绝网剧、长剧、文艺剧本写法
3. 情绪：主打逆袭、复仇、打脸、装X、守护、绝地翻盘
4. 叙事：开篇炸点，层层加码，反派持续作死，主角逐步崛起
5. 格式：全程使用Markdown排版，分区明确、条理清晰，不要多余注释

# 禁止内容
不要写拍摄脚本、镜头语言、场景道具；不要扩充无关剧情；不要使用高深词汇；不要输出英文、乱码、多余话术。
PROMPT;

    $data = [
        "model" => "deepseek-chat",
        "messages" => [
            [
                "role"=>"system",
                "content"=>"你是红果短剧平台顶级爆款编剧，严格按照要求输出符合平台流量风格的短剧内容，节奏快、冲突强、爽点足、每集留悬念。"
            ],
            [
                "role"=>"user",
                "content"=>$prompt
            ]
        ],
        "temperature"=>1.3,
        "max_tokens"=>6000
    ];

    $ch = curl_init($API_URL);
    curl_setopt_array($ch,[
        CURLOPT_RETURNTRANSFER=>true,
        CURLOPT_POST=>true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER=>[
            "Authorization: Bearer ".$API_KEY,
            "Content-Type: application/json; charset=utf-8"
        ],
        CURLOPT_POSTFIELDS=>json_encode($data,JSON_UNESCAPED_UNICODE)
    ]);

    $response = curl_exec($ch);
    $curlErr = curl_error($ch);
    curl_close($ch);

    if(!empty($curlErr)){
        $result = "接口请求异常：".$curlErr;
    }else{
        $json = json_decode($response,true);
        if(isset($json['choices'][0]['message']['content']))
        {
            $result = $json['choices'][0]['message']['content'];
        }
        else
        {
            $result = "接口返回数据异常：<br>".htmlspecialchars($response);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>红果AI短剧生成器 | 爆款剧本一键生成</title>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background: linear-gradient(135deg, #0f172a 0%, #1a2035 100%);
    font-family: "Microsoft YaHei", system-ui, sans-serif;
    color: #ffffff;
    padding: 20px 15px;
    min-height: 100vh;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
}

/* 标题样式 */
h1 {
    text-align: center;
    margin-bottom: 30px;
    font-size: clamp(26px, 5vw, 38px);
    background: linear-gradient(90deg, #ff6b6b, #ffd166);
    -webkit-background-clip: text;
    color: transparent;
    text-shadow: 0 0 20px rgba(255, 107, 107, 0.3);
    letter-spacing: 2px;
}

/* 卡片通用样式 玻璃拟态 */
.card {
    background: rgba(30, 41, 59, 0.75);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    padding: 28px;
    border-radius: 20px;
    margin-bottom: 24px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(255, 77, 79, 0.15);
}

/* 标签快捷题材 */
.tag-wrap {
    margin-bottom: 20px;
}
.tag {
    display: inline-block;
    padding: 7px 16px;
    background: linear-gradient(90deg, #ff4d4f, #ff7875);
    border-radius: 30px;
    margin: 0 10px 12px 0;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.25s ease;
    user-select: none;
}
.tag:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(255, 77, 79, 0.4);
}

/* 输入框 */
textarea {
    width: 100%;
    height: 140px;
    padding: 16px;
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 12px;
    font-size: 16px;
    background: rgba(15, 23, 42, 0.6);
    color: #fff;
    outline: none;
    resize: none;
    transition: border 0.3s ease, box-shadow 0.3s ease;
}
textarea:focus {
    border-color: #ff4d4f;
    box-shadow: 0 0 0 3px rgba(255, 77, 79, 0.2);
}
textarea::placeholder {
    color: #94a3b8;
}

/* 提交按钮 */
button {
    width: 100%;
    padding: 16px;
    margin-top: 20px;
    font-size: 18px;
    font-weight: 600;
    background: linear-gradient(90deg, #ff4d4f, #f72b2d);
    color: #fff;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    letter-spacing: 1px;
}
button:hover {
    opacity: 0.92;
    box-shadow: 0 6px 20px rgba(255, 77, 79, 0.35);
    transform: translateY(-2px);
}
button:active {
    transform: translateY(0);
}

/* 输出结果区域 */
h2 {
    font-size: 22px;
    margin-bottom: 12px;
    color: #ffd166;
}
hr {
    border: none;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    margin: 16px 0;
}
.output {
    white-space: pre-wrap;
    line-height: 2;
    font-size: 15px;
    color: #e2e8f0;
    max-height: 800px;
    overflow-y: auto;
    padding-right: 10px;
}
/* 滚动条美化 */
.output::-webkit-scrollbar {
    width: 6px;
}
.output::-webkit-scrollbar-thumb {
    background: rgba(255, 77, 79, 0.5);
    border-radius: 3px;
}
.output::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
}

/* 页脚 */
footer {
    text-align: center;
    margin-top: 40px;
    color: #64748b;
    font-size: 14px;
    padding-bottom: 20px;
}

/* 移动端适配 */
@media (max-width: 768px) {
    body {
        padding: 15px 10px;
    }
    .card {
        padding: 20px 16px;
    }
    .tag {
        padding: 6px 12px;
        font-size: 13px;
        margin-right: 6px;
    }
    textarea {
        height: 120px;
    }
}
</style>
</head>
<body>
<div class="container">
    <h1>🎬 红果AI短剧生成器</h1>

    <div class="card">
        <form method="post">
            <div class="tag-wrap">
                <div class="tag" onclick="fillTopic('非遗神话+神兽觉醒+少女逆袭')">非遗神话</div>
                <div class="tag" onclick="fillTopic('古风权谋+寒门逆袭+打脸权贵')">古风逆袭</div>
                <div class="tag" onclick="豪门恩怨+落魄千金+复仇打脸">豪门复仇</div>
                <div class="tag" onclick="fillTopic('穿越重生+废柴逆袭+系统加持')">穿越重生</div>
                <div class="tag" onclick="fillTopic('无敌系统+都市战神+全场镇压')">系统流</div>
            </div>

            <textarea name="topic" id="topicInput" placeholder="例如：非遗+山海经+苗族少女+神兽传承+逆袭"><?php echo htmlspecialchars($topic); ?></textarea>

            <button type="submit">🚀 一键生成红果爆款短剧</button>
        </form>
    </div>

    <?php if($result): ?>
    <div class="card">
        <h2>📝 生成结果</h2>
        <hr>
        <div class="output">
            <?php echo nl2br(htmlspecialchars($result)); ?>
        </div>
    </div>
    <?php endif; ?>

    <footer>
        红果AI短剧生成系统 V2.0 | 专注红果平台爆款剧本创作
    </footer>
</div>

<script>
// 快捷标签自动填充内容
function fillTopic(text){
    document.getElementById('topicInput').value = text;
    document.getElementById('topicInput').focus();
}
</script>
</body>
</html>
