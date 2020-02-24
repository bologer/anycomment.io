/**
 * Generic response envelope returned from the API.
 */
export interface ErrorEnvelope {
    code?: string | null;
    message?: string | null;
    data: {
        status: number;
    };
    additional_errors?: [];
}
