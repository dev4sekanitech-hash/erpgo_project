import { PageProps } from '@/types';
import { PaginatedData } from '@/types/common';

export interface CaregiverProfile {
    id: number;
    user_id: number;
    first_name: string;
    last_name: string;
    email: string;
    phone: string;
    address: string;
    city: string;
    province: string;
    postal_code: string;
    date_of_birth: string;
    gender: string;
    status: string;
    profile_photo: string;
    created_at: string;
    user?: {
        id: number;
        name: string;
        email: string;
    };
    languages?: CaregiverLanguage[];
    certificates?: CaregiverCertificate[];
    employment_records?: CaregiverEmploymentRecord[];
}

export interface CaregiverLanguage {
    id: number;
    caregiver_profile_id: number;
    language: string;
    proficiency: string;
}

export interface CaregiverCertificate {
    id: number;
    caregiver_profile_id: number;
    name: string;
    institution: string;
    issue_date: string;
    expiry_date: string;
    certificate_number: string;
    file_url: string;
}

export interface CaregiverEmploymentRecord {
    id: number;
    caregiver_profile_id: number;
    employer_name: string;
    position: string;
    start_date: string;
    end_date: string;
    is_current: boolean;
    responsibilities: string;
}

export interface Job {
    id: number;
    title: string;
    description: string;
    job_type: string;
    shift_pattern: string;
    city: string;
    province: string;
    is_active: boolean;
    created_by: number;
    created_at: string;
}

export interface JobApplication {
    id: number;
    job_id: number;
    caregiver_profile_id: number;
    cover_letter: string;
    status: string;
    applied_at: string;
    job?: Job;
    caregiver_profile?: CaregiverProfile;
}

export interface StaffRequest {
    id: number;
    facility_name: string;
    contact_name: string;
    email: string;
    phone: string;
    province: string;
    city: string;
    staff_needed: number;
    staff_type: string;
    start_date: string;
    end_date: string;
    shift_pattern: string;
    additional_notes: string;
    status: string;
    created_at: string;
}

export interface CareerApplication {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    phone: string;
    province: string;
    city: string;
    position_applied: string;
    cover_letter: string;
    resume_url: string;
    created_at: string;
}

export interface ContactMessage {
    id: number;
    name: string;
    email: string;
    phone: string;
    subject: string;
    message: string;
    is_read: boolean;
    created_at: string;
}

export interface StarrlightCaregiversProps extends PageProps {
    caregivers: PaginatedData<CaregiverProfile>;
}

export interface StarrlightJobsProps extends PageProps {
    jobs: PaginatedData<Job>;
}

export interface StarrlightApplicationsProps extends PageProps {
    applications: PaginatedData<JobApplication>;
}

export interface StarrlightStaffRequestsProps extends PageProps {
    requests: PaginatedData<StaffRequest>;
}

export interface StarrlightCareersProps extends PageProps {
    careers: PaginatedData<CareerApplication>;
}

export interface StarrlightContactsProps extends PageProps {
    contacts: PaginatedData<ContactMessage>;
}
