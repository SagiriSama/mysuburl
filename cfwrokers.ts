const subUrlList = [
  // 在数组中添加需要允许的 host，例如：
  'example.com',
  'sub.example.com',
];

async function handleRequest(request) {
  const url = new URL(request.url);
  const surl = url.searchParams.get('s');
  if (!surl) {
    return new Response('Authorization URL:\n' + subUrlList.join('\n'), {
      status: 404,
      statusText: 'Not Found',
      headers: { 'Content-Type': 'text/plain' },
    });
  }

  let urlCheck = false;
  try {
    const ssrUrl = new URL(surl).host;
    urlCheck = subUrlList.includes(ssrUrl);
  } catch (error) {
    // Ignore error.
  }

  if (!urlCheck) {
    return new Response(null, {
      status: 404,
      statusText: 'Not Found',
    });
  }

  // Generate a random IP address.
  const ipLong = [
    ['607649792', '608174079'],
    ['1038614528', '1039007743'],
    ['1783627776', '1784676351'],
    ['2035023872', '2035154943'],
    ['2078801920', '2079064063'],
    ['-1950089216', '-1948778497'],
    ['-1425539072', '-1425014785'],
    ['-1236271104', '-1235419137'],
    ['-770113536', '-768606209'],
    ['-569376768', '-564133889'],
  ];
  const randKey = Math.floor(Math.random() * ipLong.length);
  const ipRank = new Uint32Array(1);
  crypto.getRandomValues(ipRank);
  const randomIp =
    ipRank[0] % (ipLong[randKey][1] - ipLong[randKey][0] + 1) + parseInt(ipLong[randKey][0], 10);
  const headers = {
    'Client-IP': new Uint8Array([randomIp >> 24, randomIp >> 16, randomIp >> 8, randomIp]),
    'X-Forwarded-For': new Uint8Array([randomIp >> 24, randomIp >> 16, randomIp >> 8, randomIp]),
    'User-Agent': 'Mozilla/5.0 (Windows; U; Windows NT 5.2) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.2.149.27',
  };

  const response = await fetch(surl, { headers });
  if (!response.ok) {
    return new Response(null, { status: 404, statusText: 'Not Found' });
  }

  return response;
}

addEventListener('fetch', (event) => {
  event.respondWith(handleRequest(event.request));
});
