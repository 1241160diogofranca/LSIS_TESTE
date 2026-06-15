// Minor UX helpers for Meireles Connect (vanilla JS).
document.addEventListener('click', (ev) => {
  const menu = document.getElementById('umenu');
  if (menu && menu.classList.contains('open')) {
    if (!ev.target.closest('.user-menu')) menu.classList.remove('open');
  }
});
// auto-dismiss flash toasts after 4s
setTimeout(() => {
  const t = document.getElementById('toaster');
  if (t) t.style.opacity = '0';
  setTimeout(() => t && t.remove(), 400);
}, 4000);
// quantity controls (used on cart page)
function qtyChange(id, delta) {
  const f = document.getElementById('cart-form-'+id);
  const inp = f.querySelector('input[name="qty"]');
  let v = Math.max(1, parseInt(inp.value || '1', 10) + delta);
  inp.value = v;
  f.submit();
}
// price slider (catalog)
function updatePriceLabel(val) {
  const l = document.getElementById('price-label');
  if (l) l.textContent = (parseInt(val,10)).toLocaleString('pt-PT') + ' €';
}
