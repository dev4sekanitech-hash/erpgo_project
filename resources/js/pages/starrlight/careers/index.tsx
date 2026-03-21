import { Head, usePage } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Card, CardContent } from "@/components/ui/card";
import { DataTable } from "@/components/ui/data-table";
import { Button } from "@/components/ui/button";
import { Eye, Briefcase } from "lucide-react";
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from "@/components/ui/tooltip";
import NoRecordsFound from "@/components/no-records-found";
import { StarrlightCareersProps, CareerApplication } from "../types";

export default function CareersIndex() {
    const { t } = useTranslation();
    const { careers } = usePage<StarrlightCareersProps>().props;

    const tableColumns = [
        {
            key: "first_name",
            header: t("Name"),
            render: (_: any, record: CareerApplication) => (
                <span>
                    {record.first_name} {record.last_name}
                </span>
            ),
        },
        {
            key: "email",
            header: t("Email"),
        },
        {
            key: "phone",
            header: t("Phone"),
        },
        {
            key: "position_applied",
            header: t("Position"),
        },
        {
            key: "city",
            header: t("City"),
        },
        {
            key: "province",
            header: t("Province"),
        },
        {
            key: "created_at",
            header: t("Applied At"),
        },
        {
            key: "actions",
            header: t("Actions"),
            render: (_: any, record: CareerApplication) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        <Tooltip delayDuration={0}>
                            <TooltipTrigger asChild>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    onClick={() =>
                                        (window.location.href = route(
                                            "starrlight.careers.show",
                                            record.id,
                                        ))
                                    }
                                    className="h-8 w-8 p-0 text-blue-600 hover:text-blue-700"
                                >
                                    <Eye className="h-4 w-4" />
                                </Button>
                            </TooltipTrigger>
                            <TooltipContent>
                                <p>{t("View")}</p>
                            </TooltipContent>
                        </Tooltip>
                    </TooltipProvider>
                </div>
            ),
        },
    ];

    return (
        <AuthenticatedLayout
            breadcrumbs={[{ label: t("Starrlight") }, { label: t("Careers") }]}
            pageTitle={t("Manage Career Applications")}
        >
            <Head title={t("Careers")} />

            <Card className="shadow-sm">
                <CardContent className="p-0">
                    <DataTable
                        data={careers.data}
                        columns={tableColumns}
                        emptyState={
                            <NoRecordsFound
                                icon={Briefcase}
                                title={t("No career applications found")}
                                description={t(
                                    "Career applications will appear here.",
                                )}
                                className="h-auto"
                            />
                        }
                    />
                </CardContent>
            </Card>
        </AuthenticatedLayout>
    );
}
