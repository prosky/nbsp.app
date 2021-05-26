export const space = '^|$|;| | |&nbsp;|\\(|\\n|>';
export function onDocumentReady(callback) {
    if (document.readyState !== 'loading') {
        callback.call(this);
    } else {
        document.addEventListener('DOMContentLoaded', callback);
    }
}