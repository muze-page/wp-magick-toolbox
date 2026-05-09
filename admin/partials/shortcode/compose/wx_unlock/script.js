(function() {
  var btn = document.getElementById('mabox-wx-verify-btn');
  var input = document.getElementById('mabox-wx-code');
  var error = document.getElementById('mabox-wx-error');

  if (!btn || !input) return;

  btn.addEventListener('click', function() {
    var code = input.value.trim();
    if (!code) {
      error.textContent = '请输入验证码';
      return;
    }

    btn.disabled = true;
    btn.textContent = '验证中...';
    error.textContent = '';

    var xhr = new XMLHttpRequest();
    xhr.open('POST', maboxWxUnlock.ajax_url, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
      try {
        var res = JSON.parse(xhr.responseText);
        if (res.success) {
          error.textContent = '';
          location.reload();
        } else {
          error.textContent = res.data && res.data.message ? res.data.message : '验证码错误';
        }
      } catch (e) {
        error.textContent = '网络错误，请重试';
      }
      btn.disabled = false;
      btn.textContent = '解锁';
    };
    xhr.onerror = function() {
      error.textContent = '网络错误，请重试';
      btn.disabled = false;
      btn.textContent = '解锁';
    };
    xhr.send('action=mabox_wx_unlock_verify&code=' + encodeURIComponent(code) + '&nonce=' + maboxWxUnlock.nonce);
  });

  input.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
      btn.click();
    }
  });
})();
