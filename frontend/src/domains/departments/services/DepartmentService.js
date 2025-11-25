import {BaseApiService} from "@/core/services/BaseApiService.js";

class DepartmentService extends BaseApiService {
    constructor() {
        super('departments');
    }
}

export const departmentService = new DepartmentService();
