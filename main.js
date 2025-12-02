// assets/js/main.js (vanilla)
document.addEventListener('DOMContentLoaded', function(){
  // sample global helper to fetch JSON with error handling
  window.apiFetch = async (url, opts = {}) => {
    const res = await fetch(url, opts);
    const ct = res.headers.get('content-type') || '';
    if (ct.includes('application/json')) return res.json();
    return res.text();
  };
});
