// Add the server-issued CSRF token to same-origin requests that change data.
(() => {
  const originalFetch = window.fetch.bind(window);
  window.fetch = (input, options = {}) => {
    const url = new URL(input instanceof Request ? input.url : input, location.href);
    const method = (options.method || (input instanceof Request ? input.method : 'GET')).toUpperCase();
    if (url.origin === location.origin && !['GET', 'HEAD', 'OPTIONS'].includes(method)) {
      const headers = new Headers(options.headers || (input instanceof Request ? input.headers : {}));
      headers.set('X-CSRF-Token', document.querySelector('meta[name="csrf-token"]')?.content || '');
      options = { ...options, headers };
    }
    return originalFetch(input, options);
  };
})();
