/**
 * Generic response envelope returned from the API.
 */
export interface BasicEnvelope<Response> {
    error?: string | null;
    error_code?: string | null;
    response?: Response;
    status?: 'ok' | 'fail';
}
