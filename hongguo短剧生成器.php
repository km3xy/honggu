<?php
$result = '';

if(isset($_POST['topic']))
{
    $topic = trim($_POST['topic']);

    $apiKey = 'sk-你的deepseek  api';

    $prompt = "
你是一位红果短剧编剧大师。

根据用户关键词生成：

1、剧名
2、题材
3、一句话故事
4、主角设定
5、10集大纲
6、AI绘图提示词
7、AI视频提示词

关键词：
{$topic}

要求：

适合红果短剧平台

爽点强

反转强

格式清晰
";

    $data = [
        "model"=>"deepseek-chat",
        "messages"=>[
            [
                "role"=>"user",
                "content"=>$prompt
            ]
        ],
        "temperature"=>0.9,
        "max_tokens"=>3000
    ];

    $ch = curl_init();

    curl_setopt_array($ch,[
        CURLOPT_URL=>"https://api.deepseek.com/v1/chat/completions",
        CURLOPT_RETURNTRANSFER=>true,
        CURLOPT_POST=>true,
        CURLOPT_HTTPHEADER=>[
            "Content-Type: application/json",
            "Authorization: Bearer ".$apiKey
        ],
        CURLOPT_POSTFIELDS=>json_encode($data)
    ]);

    $response = curl_exec($ch);

    curl_close($ch);

    $json = json_decode($response,true);

    if(isset($json['choices'][0]['message']['content']))
    {
        $result = $json['choices'][0]['message']['content'];
    }
    else
    {
        $result = "生成失败：<br><pre>".htmlspecialchars($response)."</pre>";
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>红果短剧生成器</title>

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
}

body{
background:#0f172a;
font-family:Arial;
padding:20px;
color:#fff;
}

.container{
max-width:900px;
margin:auto;
}

.title{
font-size:32px;
font-weight:bold;
text-align:center;
margin-bottom:20px;
}

.card{
background:#1e293b;
padding:20px;
border-radius:15px;
margin-bottom:20px;
}

textarea{
width:100%;
height:120px;
padding:15px;
border:none;
border-radius:10px;
font-size:16px;
resize:none;
}

button{
width:100%;
padding:15px;
background:#f97316;
color:#fff;
border:none;
border-radius:10px;
font-size:18px;
cursor:pointer;
margin-top:15px;
}

button:hover{
opacity:.9;
}

.result{
white-space:pre-wrap;
line-height:1.8;
font-size:15px;
}

.footer{
text-align:center;
margin-top:20px;
color:#94a3b8;
}

</style>
</head>
<body>

<div class="container">

<div class="title">
🎬 红果短剧生成器
</div>

<div class="card">

<form method="post">

<textarea
name="topic"
placeholder="输入关键词，例如：白马神兽、重生首富、非遗神话"
><?php echo isset($_POST['topic']) ? htmlspecialchars($_POST['topic']) : ''; ?></textarea>

<button type="submit">
🚀 一键生成短剧
</button>

</form>

</div>

<?php if($result!=''): ?>

<div class="card">

<h2>生成结果</h2>

<div class="result">
<?php echo nl2br(htmlspecialchars($result)); ?>
</div>

</div>

<?php endif; ?>

<div class="footer">
DeepSeek API + 红果短剧生成器
</div>

</div>

</body>
</html>
