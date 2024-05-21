

// 更新倒计时的函数
function updateCountdown() {
    var currentDate = new Date(); // 当前日期和时间
  
    // 计算剩余时间
    var remainingTime = targetDate - currentDate;
  
    // 如果目标日期已过，则显示倒计时结束
    if (remainingTime <= 0) {
      document.getElementById("countdown").innerHTML = "倒计时结束";
      return;
    }
  
    // 计算剩余的天、小时、分钟和秒
    var days = Math.floor(remainingTime / (1000 * 60 * 60 * 24));
    var hours = Math.floor((remainingTime / (1000 * 60 * 60)) % 24);
    var minutes = Math.floor((remainingTime / 1000 / 60) % 60);
    var seconds = Math.floor((remainingTime / 1000) % 60);
  
    // 格式化时间并显示在页面上
    var countdownString =
      ' <ul class="countdown-content"><li> <span class="digits days">' +
      days +
      '</span> <i>:</i> <span class="label">天</span></li><li> <span class="digits hours">' +
      hours.toString().padStart(2, "0") +
      '</span> <i>:</i> <span class="label">时' +
      '</span></li><li> <span class="digits minutes">' +
      minutes.toString().padStart(2, "0") +
      
      '</span> <i>:</i> <span class="label">分</span> </li><li> <span class="digits seconds">' +
      seconds.toString().padStart(2, "0") +
      '</span> <span class="label">秒</span> </li></ul>';
    document.getElementById("countdown").innerHTML = countdownString;
  
    // 每秒钟更新一次倒计时
    setTimeout(updateCountdown, 1000);
  }
  
  // 页面加载完成后开始倒计时
  window.onload = function () {
    updateCountdown();
  };
  