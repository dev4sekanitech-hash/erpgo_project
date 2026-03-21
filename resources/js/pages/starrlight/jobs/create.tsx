import { useState } from "react";
import { Head, router, usePage } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import { PageProps } from "@/types";

interface JobFormData {
    title: string;
    description: string;
    job_type: string;
    shift_pattern: string;
    city: string;
    province: string;
}

export default function CreateJob() {
    const { t } = useTranslation();
    const { auth } = usePage<PageProps>().props;

    const [formData, setFormData] = useState<JobFormData>({
        title: "",
        description: "",
        job_type: "",
        shift_pattern: "",
        city: "",
        province: "",
    });

    const [errors, setErrors] = useState<Record<string, string>>({});

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        router.post(route("starrlight.jobs.store"), formData, {
            onError: (errors) => {
                setErrors(errors);
            },
        });
    };

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t("Starrlight") },
                { label: t("Jobs"), href: route("starrlight.jobs.index") },
                { label: t("Create") },
            ]}
            pageTitle={t("Create Job")}
        >
            <Head title={t("Create Job")} />

            <form onSubmit={handleSubmit}>
                <Card className="shadow-sm">
                    <CardHeader>
                        <CardTitle>{t("Job Details")}</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <Label htmlFor="title">{t("Title")} *</Label>
                                <Input
                                    id="title"
                                    value={formData.title}
                                    onChange={(e) =>
                                        setFormData({
                                            ...formData,
                                            title: e.target.value,
                                        })
                                    }
                                    className="mt-1"
                                    placeholder={t("Job title")}
                                />
                                {errors.title && (
                                    <p className="text-red-500 text-sm mt-1">
                                        {errors.title}
                                    </p>
                                )}
                            </div>

                            <div>
                                <Label htmlFor="job_type">
                                    {t("Job Type")} *
                                </Label>
                                <Select
                                    value={formData.job_type}
                                    onValueChange={(value) =>
                                        setFormData({
                                            ...formData,
                                            job_type: value,
                                        })
                                    }
                                >
                                    <SelectTrigger className="mt-1">
                                        <SelectValue
                                            placeholder={t("Select job type")}
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="full_time">
                                            {t("Full Time")}
                                        </SelectItem>
                                        <SelectItem value="part_time">
                                            {t("Part Time")}
                                        </SelectItem>
                                        <SelectItem value="casual">
                                            {t("Casual")}
                                        </SelectItem>
                                        <SelectItem value="contract">
                                            {t("Contract")}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                {errors.job_type && (
                                    <p className="text-red-500 text-sm mt-1">
                                        {errors.job_type}
                                    </p>
                                )}
                            </div>

                            <div>
                                <Label htmlFor="shift_pattern">
                                    {t("Shift Pattern")} *
                                </Label>
                                <Select
                                    value={formData.shift_pattern}
                                    onValueChange={(value) =>
                                        setFormData({
                                            ...formData,
                                            shift_pattern: value,
                                        })
                                    }
                                >
                                    <SelectTrigger className="mt-1">
                                        <SelectValue
                                            placeholder={t(
                                                "Select shift pattern",
                                            )}
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="day_shift">
                                            {t("Day Shift")}
                                        </SelectItem>
                                        <SelectItem value="night_shift">
                                            {t("Night Shift")}
                                        </SelectItem>
                                        <SelectItem value="day_night_rotation">
                                            {t("Day/Night Rotation")}
                                        </SelectItem>
                                        <SelectItem value="weekends">
                                            {t("Weekends Only")}
                                        </SelectItem>
                                        <SelectItem value="flexible">
                                            {t("Flexible")}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                {errors.shift_pattern && (
                                    <p className="text-red-500 text-sm mt-1">
                                        {errors.shift_pattern}
                                    </p>
                                )}
                            </div>

                            <div>
                                <Label htmlFor="city">{t("City")} *</Label>
                                <Input
                                    id="city"
                                    value={formData.city}
                                    onChange={(e) =>
                                        setFormData({
                                            ...formData,
                                            city: e.target.value,
                                        })
                                    }
                                    className="mt-1"
                                    placeholder={t("City")}
                                />
                                {errors.city && (
                                    <p className="text-red-500 text-sm mt-1">
                                        {errors.city}
                                    </p>
                                )}
                            </div>

                            <div>
                                <Label htmlFor="province">
                                    {t("Province")} *
                                </Label>
                                <Select
                                    value={formData.province}
                                    onValueChange={(value) =>
                                        setFormData({
                                            ...formData,
                                            province: value,
                                        })
                                    }
                                >
                                    <SelectTrigger className="mt-1">
                                        <SelectValue
                                            placeholder={t("Select province")}
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="Ontario">
                                            Ontario
                                        </SelectItem>
                                        <SelectItem value="British Columbia">
                                            British Columbia
                                        </SelectItem>
                                        <SelectItem value="Alberta">
                                            Alberta
                                        </SelectItem>
                                        <SelectItem value="Quebec">
                                            Quebec
                                        </SelectItem>
                                        <SelectItem value="Manitoba">
                                            Manitoba
                                        </SelectItem>
                                        <SelectItem value="Saskatchewan">
                                            Saskatchewan
                                        </SelectItem>
                                        <SelectItem value="Nova Scotia">
                                            Nova Scotia
                                        </SelectItem>
                                        <SelectItem value="New Brunswick">
                                            New Brunswick
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                {errors.province && (
                                    <p className="text-red-500 text-sm mt-1">
                                        {errors.province}
                                    </p>
                                )}
                            </div>
                        </div>

                        <div>
                            <Label htmlFor="description">
                                {t("Description")} *
                            </Label>
                            <Textarea
                                id="description"
                                value={formData.description}
                                onChange={(e) =>
                                    setFormData({
                                        ...formData,
                                        description: e.target.value,
                                    })
                                }
                                className="mt-1"
                                rows={5}
                                placeholder={t("Job description")}
                            />
                            {errors.description && (
                                <p className="text-red-500 text-sm mt-1">
                                    {errors.description}
                                </p>
                            )}
                        </div>
                    </CardContent>
                </Card>

                <div className="flex justify-end gap-2 mt-4">
                    <Button
                        type="button"
                        variant="outline"
                        onClick={() =>
                            router.get(route("starrlight.jobs.index"))
                        }
                    >
                        {t("Cancel")}
                    </Button>
                    <Button type="submit">{t("Create Job")}</Button>
                </div>
            </form>
        </AuthenticatedLayout>
    );
}
