import AppLogoIcon from '@/components/app-logo-icon';

export default function AppLogo() {
    return (
        <>
            <div className="flex h-10 w-10 shrink-0 items-center justify-center bg-transparent text-sidebar-primary-foreground">
                <AppLogoIcon className="h-full w-full" />
            </div>
            <div className="ml-1 grid flex-1 text-left text-sm">
                <span className="mb-0.5 truncate leading-tight font-semibold">
                    Polinotes
                </span>
            </div>
        </>
    );
}
