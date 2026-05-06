<?php

namespace App\Constants;

final class RoleAndPermissions
{
    public const ADMIN = 'admin';
    public const ORGANIZATION_ADMIN = 'organization_admin';
    public const MANAGEMENT_ADMIN = 'management_admin';
    public const DIRECTORATE_ADMIN = 'directorate_admin';
    public const REGIONAL_ADMIN = 'regional_admin';
    public const DEPARTMENT_ADMIN = 'department_admin';
    public const HEAD_OF_UNIT = 'head_of_unit';
    public const ORGANIZATION_PERSONNEL = 'organization_personnel';
    public const AUDITOR = 'auditor';

    public const PROJECT_MANAGER = 'project_manager';
    public const PROJECT_MEMBER = 'project_member';
    public const CONTRACTOR = 'contractor';
    public const CONTRACTOR_PERSONNEL = 'contractor_personnel';
    public const CONSULTANT = 'consultant';

    public const SYSTEM_ROLES = [
        self::ADMIN,
        self::ORGANIZATION_ADMIN,
        self::MANAGEMENT_ADMIN,
        self::DIRECTORATE_ADMIN,
        self::REGIONAL_ADMIN,
        self::DEPARTMENT_ADMIN,
        self::HEAD_OF_UNIT,
        self::ORGANIZATION_PERSONNEL,
        self::AUDITOR,
    ];

    public const PROJECT_ROLES = [
        self::PROJECT_MANAGER,
        self::PROJECT_MEMBER,
        self::CONTRACTOR,
        self::CONTRACTOR_PERSONNEL,
        self::CONSULTANT,
    ];

    public const ROLES = [
        ...self::SYSTEM_ROLES,
        ...self::PROJECT_ROLES,
    ];
}
