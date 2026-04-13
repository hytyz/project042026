import type { ImgHTMLAttributes } from 'react';
import { cn } from '@/lib/utils';

export default function AppLogoIcon({ className, ...props }: ImgHTMLAttributes<HTMLImageElement>) {
    return (
        <img
            src="/bootleg-policon.png"
            alt=""
            className={cn(
                'rounded-sm bg-white p-px object-contain ring-1 ring-black/10',
                className,
            )}
            {...props}
        />
    );
}
