export function formatTimezoneOffset(minutes) {
    if (typeof minutes !== 'number') {
        return '';
    }

    const hours = minutes / 60;
    const sign = hours >= 0 ? '+' : '-';
    const normalized = Math.abs(hours).toString().padStart(2, '0');

    return `UTC${sign}${normalized}:00`;
}
