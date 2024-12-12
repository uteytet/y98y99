<?php
// 设置允许跨域请求
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// API配置
$api_config = [
    'base_url' => 'https://www.703dm.com/api',
    'admin_token' => 'YOUR_ADMIN_TOKEN', // 替换为您的管理员token
    'access_key' => 'YOUR_ACCESS_KEY'    // 替换为您的access key
];

// 获取视频信息
function getVideoInfo($video_id) {
    global $api_config;
    
    // 构建API请求URL
    $url = $api_config['base_url'] . '/admin/video/' . $video_id;
    
    // 设置请求头
    $headers = [
        'Authorization: Bearer ' . $api_config['admin_token'],
        'X-Access-Key: ' . $api_config['access_key'],
        'Content-Type: application/json'
    ];
    
    // 设置curl选项
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_SSL_VERIFYPEER => false, // 如果需要的话
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CUSTOMREQUEST => 'GET'
    ]);
    
    // 执行请求
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // 错误处理
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['error' => 'Curl error: ' . $error];
    }
    
    curl_close($ch);
    
    // 检查响应状态
    if ($http_code !== 200) {
        return ['error' => 'API request failed with status: ' . $http_code];
    }
    
    // 解析响应
    $data = json_decode($response, true);
    if (!$data) {
        return ['error' => 'Invalid JSON response'];
    }
    
    return $data;
}

try {
    // 获取请求参数
    $video_id = isset($_GET['id']) ? $_GET['id'] : '1';
    
    // 获取视频信息
    $video_info = getVideoInfo($video_id);
    
    // 返回结果
    if (isset($video_info['error'])) {
        http_response_code(500);
        echo json_encode(['error' => $video_info['error']]);
    } else {
        echo json_encode([
            'success' => true,
            'data' => $video_info,
            'play_url' => $video_info['play_url'] ?? null
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 