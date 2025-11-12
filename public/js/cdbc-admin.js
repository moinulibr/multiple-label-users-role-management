// public/js/cdbc-admin.js
window.cdbcNextProfileIndex = window.cdbcNextProfileIndex || 1000;
function cdbcAddProfileRow() {
  const tpl = document.getElementById('cdbc-profile-row-template').innerHTML;
  const idx = window.cdbcNextProfileIndex++;
  const html = tpl.replace(/__INDEX__/g, idx);
  document.getElementById('cdbc-profiles-wrapper').insertAdjacentHTML('beforeend', html);
}
