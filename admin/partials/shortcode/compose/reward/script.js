function maboxRewardSwitchTab(btn) {
  var tabList = btn.parentElement;
  var tabs = tabList.querySelectorAll('.mabox-reward-tab');
  tabs.forEach(function(t) { t.classList.remove('active'); });
  btn.classList.add('active');

  var target = btn.getAttribute('data-target');
  var qrs = tabList.parentElement.querySelector('.mabox-reward-qrs');
  var qrDivs = qrs.querySelectorAll('.mabox-reward-qr');
  qrDivs.forEach(function(q) { q.classList.remove('active'); });
  var targetQr = qrs.querySelector('#' + target);
  if (targetQr) targetQr.classList.add('active');
}
