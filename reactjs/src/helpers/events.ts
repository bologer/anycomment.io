/**
 * Fires provided event with payload.
 * @param name
 * @param payload
 */
export function fireEvent(events, name: string, payload = undefined) {
    if (events && events[name]) {
        const eventFunc = events[name];
        if (typeof eventFunc === 'function') {
            eventFunc(payload);
        }
    }
}
