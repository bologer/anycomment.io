/**
 * Represents generic interface for API response when it returns batch data.
 */
export interface ListResponse<Item> {
    items: Item[];
    meta: {
        total_count: number;
        page_count: number;
        current_page: number;
        per_page: number;
    };
}
