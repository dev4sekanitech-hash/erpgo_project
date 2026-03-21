import { Head, usePage, router } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import { useDeleteHandler } from "@/hooks/useDeleteHandler";
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Card, CardContent } from "@/components/ui/card";
import { DataTable } from "@/components/ui/data-table";
import { ConfirmationDialog } from "@/components/ui/confirmation-dialog";
import { Button } from "@/components/ui/button";
import { Eye, Trash2, MessageSquare } from "lucide-react";
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from "@/components/ui/tooltip";
import NoRecordsFound from "@/components/no-records-found";
import { StarrlightContactsProps, ContactMessage } from "../types";

export default function ContactsIndex() {
    const { t } = useTranslation();
    const { contacts, auth } = usePage<StarrlightContactsProps>().props;

    const { deleteState, openDeleteDialog, closeDeleteDialog, confirmDelete } =
        useDeleteHandler({
            routeName: "starrlight.contacts.destroy",
            defaultMessage: t("Are you sure you want to delete this message?"),
        });

    const tableColumns = [
        {
            key: "name",
            header: t("Name"),
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
            key: "subject",
            header: t("Subject"),
        },
        {
            key: "is_read",
            header: t("Status"),
            render: (value: boolean) => (
                <span
                    className={`px-2 py-1 rounded-full text-xs ${
                        value
                            ? "bg-green-100 text-green-800"
                            : "bg-yellow-100 text-yellow-800"
                    }`}
                >
                    {value ? t("Read") : t("Unread")}
                </span>
            ),
        },
        {
            key: "created_at",
            header: t("Date"),
        },
        {
            key: "actions",
            header: t("Actions"),
            render: (_: any, record: ContactMessage) => (
                <div className="flex gap-1">
                    <TooltipProvider>
                        <Tooltip delayDuration={0}>
                            <TooltipTrigger asChild>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    onClick={() =>
                                        (window.location.href = route(
                                            "starrlight.contacts.show",
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
                        {auth.user?.permissions?.includes(
                            "manage-starrlight-contacts",
                        ) && (
                            <Tooltip delayDuration={0}>
                                <TooltipTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() =>
                                            openDeleteDialog(record.id)
                                        }
                                        className="h-8 w-8 p-0 text-destructive hover:text-destructive"
                                    >
                                        <Trash2 className="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>{t("Delete")}</p>
                                </TooltipContent>
                            </Tooltip>
                        )}
                    </TooltipProvider>
                </div>
            ),
        },
    ];

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t("Starrlight") },
                { label: t("Contact Messages") },
            ]}
            pageTitle={t("Manage Contact Messages")}
        >
            <Head title={t("Contact Messages")} />

            <Card className="shadow-sm">
                <CardContent className="p-0">
                    <DataTable
                        data={contacts.data}
                        columns={tableColumns}
                        emptyState={
                            <NoRecordsFound
                                icon={MessageSquare}
                                title={t("No messages found")}
                                description={t(
                                    "Contact messages will appear here.",
                                )}
                                className="h-auto"
                            />
                        }
                    />
                </CardContent>
            </Card>

            <ConfirmationDialog
                open={deleteState.isOpen}
                onClose={closeDeleteDialog}
                onConfirm={confirmDelete}
                title={t("Delete Message")}
                message={deleteState.message}
            />
        </AuthenticatedLayout>
    );
}
