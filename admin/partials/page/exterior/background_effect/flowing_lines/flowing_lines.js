function i() {
  if ((ctx.clearRect(0, 0, w, h), firstTime))
    for (
      q = [
        { x: 0, y: 0.7 * h + bandwidth, v_x: 0, v_y: 0 },
        { x: 0, y: 0.7 * h - bandwidth, v_x: 0, v_y: 0 },
      ],
        verticals.push(q[0]),
        verticals.push(q[1]);
      q[1].x < w + bandwidth;

    )
      firstDraw(q[0], q[1]);
  else for (var t = 0, e = 1; e < verticals.length - 1; ) tweak(t, e), t++, e++;
  (firstTime = !1), requestAnimationFrame(i);
}
function go(t) {
  random() < 0.5 || (t.v_x += 0.1 * (random() - 0.5)),
    random() < 0.5 || (t.v_y += 0.1 * (random() - 0.499));
  var e = 0.3;
  (t.v_x = Math.max(Math.min(t.v_x, e), -e)),
    (t.v_y = Math.max(Math.min(t.v_y, e), -e)),
    (t.x += t.v_x),
    (t.y += t.v_y),
    (t.x < 0 || t.x > w) && (t.v_x *= -1.01),
    (t.y < 0 || t.y > h) && (t.v_y *= -1.01);
}
function tweak(t, e) {
  ctx.beginPath();
  var i = verticals[t],
    a = verticals[e],
    o = verticals_side[t],
    r = fillStyles[t];
  go(a),
    ctx.moveTo(i.x, i.y),
    ctx.lineTo(a.x, a.y),
    go(o),
    ctx.lineTo(o.x, o.y),
    ctx.closePath(),
    (ctx.strokeStyle = r),
    ctx.stroke();
}
function firstDraw(t, e) {
  ctx.beginPath(), ctx.moveTo(t.x, t.y), ctx.lineTo(e.x, e.y);
  var i = e.x + (2 * random() - 0.25) * bandwidth,
    a = y(e.y);
  ctx.lineTo(i, a),
    ctx.closePath(),
    (r -= twoPi / -50),
    (ctx.strokeStyle =
      "#" +
      (
        ((127 * cos(r) + 128) << 16) |
        ((127 * cos(r + twoPi / 3) + 128) << 8) |
        (127 * cos(r + (twoPi / 3) * 2) + 128)
      ).toString(16)),
    ctx.stroke(),
    (q[0] = q[1]),
    (q[1] = { x: i, y: a, v_x: 0, v_y: 0 }),
    verticals.push(q[1]),
    fillStyles.push(ctx.strokeStyle),
    verticals_side.push(q[1]);
}
function y(t) {
  var e = t + (2 * random() - 1.1) * bandwidth;
  return e > h || 0 > e ? y(t) : e;
}
document.addEventListener("touchmove", function (t) {
  t.preventDefault();
});
var convas = document.getElementsByTagName("canvas")[0],
  ctx = convas.getContext("2d"),
  pr = window.devicePixelRatio || 1,
  w = window.innerWidth,
  h = window.innerHeight,
  bandwidth = 90,
  q,
  r = 0,
  twoPi = 2 * Math.PI,
  cos = Math.cos,
  random = Math.random;
(convas.width = w * pr),
  (convas.height = h * pr),
  ctx.scale(pr, pr),
  (ctx.globalAlpha = 0.05);
var verticals = [],
  verticals_side = [],
  fillStyles = [],
  firstTime = !0;
requestAnimationFrame(i);
