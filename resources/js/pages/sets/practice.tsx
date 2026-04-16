import { Head, router } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import { Badge } from '@/components/ui/badge';
import { ArrowLeft, RotateCcw, Check, X, FlipHorizontal } from 'lucide-react';

interface Flashcard {
    id: number;
    question: string;
    answer: string;
}

interface SetData {
    id: number;
    title: string;
    flashcards: Flashcard[];
    tags: { id: number; name: string }[];
}

type CardRating = 'known' | 'hard' | 'skipped';

export default function SetPractice({ id }: { id: string }) {
    const [setData, setSetData] = useState<SetData | null>(null);
    const [currentIndex, setCurrentIndex] = useState(0);
    const [isFlipped, setIsFlipped] = useState(false);
    const [results, setResults] = useState<Record<number, CardRating>>({});
    const [isComplete, setIsComplete] = useState(false);
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        fetchSet();
    }, [id]);

    const fetchSet = () => {
        fetch(`/api/sets/${id}`, {
            credentials: 'include',
            headers: { Accept: 'application/json' },
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.success && data.data.flashcards.length > 0) {
                    setSetData(data.data);
                } else {
                    setIsLoading(false);
                    setSetData(null);
                }
                setIsLoading(false);
            })
            .catch(() => {
                setIsLoading(false);
                setSetData(null);
            });
    };

    const handleRate = (rating: CardRating) => {
        if (!setData) {
            return;
        }

        setResults((prev) => ({ ...prev, [card.id]: rating }));

        if (currentIndex < setData.flashcards.length - 1) {
            setCurrentIndex((index) => index + 1);
            setIsFlipped(false);

            return;
        }

        setIsComplete(true);
    };

    const handleRestart = () => {
        setCurrentIndex(0);
        setIsFlipped(false);
        setResults({});
        setIsComplete(false);
    };

    const handleFlip = () => {
        setIsFlipped((flipped) => !flipped);
    };

    if (isLoading) {
        return (
            <>
                <Head title="Practice Flashcards" />
                <div className="flex h-full items-center justify-center">
                    <p className="text-muted-foreground">Loading...</p>
                </div>
            </>
        );
    }

    if (!setData) {
        return (
            <>
                <Head title="Practice Flashcards" />
                <div className="flex h-full flex-1 flex-col items-center justify-center gap-4 p-6">
                    <h2 className="text-2xl font-bold">No Cards to Practice</h2>
                    <p className="text-muted-foreground">
                        This set has no flashcards. Add some first!
                    </p>
                    <Button onClick={() => router.visit('/sets')}>
                        Back to Sets
                    </Button>
                </div>
            </>
        );
    }

    const card = setData.flashcards[currentIndex];

    const knownCount = Object.values(results).filter(
        (r) => r === 'known',
    ).length;
    const hardCount = Object.values(results).filter((r) => r === 'hard').length;
    const progress = ((currentIndex + 1) / setData.flashcards.length) * 100;

    const knownCards = [...setData.flashcards].filter(
        (card) => results[card.id] === 'known',
    );

    if (isComplete) {
        return (
            <>
                <Head title="Practice Complete" />
                <div className="flex h-full flex-1 flex-col items-center justify-center gap-6 p-6">
                    <div className="text-center">
                        <h1 className="text-3xl font-bold tracking-tight">
                            Practice Complete!
                        </h1>
                        <p className="mt-2 text-muted-foreground">
                            Here is how you did
                        </p>
                    </div>

                    <Card className="w-full max-w-md">
                        <CardContent className="flex items-center justify-around pt-8">
                            <div className="text-center">
                                <div className="flex items-center justify-center gap-2">
                                    <Check className="h-5 w-5 text-green-500" />
                                    <span className="text-3xl font-bold text-green-500">
                                        {knownCount}
                                    </span>
                                </div>
                                <p className="mt-1 text-sm text-muted-foreground">
                                    Known
                                </p>
                            </div>
                            <div className="text-center">
                                <div className="flex items-center justify-center gap-2">
                                    <X className="h-5 w-5 text-amber-500" />
                                    <span className="text-3xl font-bold text-amber-500">
                                        {hardCount}
                                    </span>
                                </div>
                                <p className="mt-1 text-sm text-muted-foreground">
                                    Need Work
                                </p>
                            </div>
                        </CardContent>
                    </Card>

                    {knownCards.length > 0 && (
                        <Card className="w-full max-w-md">
                            <CardContent className="pt-6">
                                <h3 className="mb-3 flex items-center gap-2 font-semibold">
                                    <Check className="h-4 w-4 text-green-500" />
                                    Cards You Knew
                                </h3>
                                <ul className="space-y-2">
                                    {knownCards.map((card) => (
                                        <li key={card.id} className="text-sm">
                                            <span className="font-medium">
                                                {card.question}
                                            </span>
                                        </li>
                                    ))}
                                </ul>
                            </CardContent>
                        </Card>
                    )}

                    <div className="flex gap-4">
                        <Button
                            variant="outline"
                            onClick={() => router.visit('/sets')}
                        >
                            <ArrowLeft className="mr-2 h-4 w-4" />
                            Back to Sets
                        </Button>
                        <Button onClick={handleRestart}>
                            <RotateCcw className="mr-2 h-4 w-4" />
                            Practice Again
                        </Button>
                    </div>
                </div>
            </>
        );
    }

    return (
        <>
            <Head title={`Practice: ${setData.title}`} />
            <div className="flex h-full flex-1 flex-col items-center gap-6 p-6">
                {/* header */}
                <div className="flex w-full max-w-2xl items-center justify-between">
                    <Button
                        variant="ghost"
                        size="icon"
                        onClick={() => router.visit(`/sets/`)}
                    >
                        <ArrowLeft className="h-5 w-5" />
                    </Button>
                    <div className="mx-4 flex-1">
                        <h1 className="text-center text-xl font-bold">
                            {setData.title}
                        </h1>
                        <p className="text-center text-sm text-muted-foreground">
                            Card {currentIndex + 1} of{' '}
                            {setData.flashcards.length}
                        </p>
                    </div>
                    <div className="flex gap-2">
                        {setData.tags.slice(0, 2).map((tag) => (
                            <Badge
                                key={tag.id}
                                variant="secondary"
                                className="text-xs"
                            >
                                {tag.name}
                            </Badge>
                        ))}
                    </div>
                </div>

                {/* progress */}
                <div className="w-full max-w-2xl">
                    <Progress value={progress} className="h-2" />
                </div>

                {/* flashcard */}
      <Card
    onClick={handleFlip}
    className={`w-full max-w-2xl cursor-pointer select-none
        [perspective:1200px]
        transition-all duration-300
        hover:scale-[1.02]
        ${isFlipped ? 'shadow-xl' : 'shadow-md'}
    `}
>
    <div
        className={`relative min-h-[320px] w-full
            transition-transform duration-500 ease-in-out
            [transform-style:preserve-3d]
            ${isFlipped ? 'rotate-y-180' : ''}
        `}
    >
        {/* front - question */}
        <div className="absolute inset-0 backface-hidden flex items-center justify-center">
           {isFlipped ? "" : ( <CardContent className="flex flex-col items-center justify-center gap-4 px-8 py-12 text-center">
                <div className="text-sm text-muted-foreground flex items-center gap-2">
                    <FlipHorizontal className="h-4 w-4" />
                    Click to reveal answer
                </div>

                <div className="max-w-lg text-xl leading-relaxed font-medium">
                    {card.question}
                </div>

                <div className="text-sm text-muted-foreground">
                    Question
                </div>
            </CardContent> )}
        </div>

        {/* back - answer */}
        { !isFlipped ? "" : (<div className="absolute inset-0 backface-hidden rotate-y-180 flex items-center justify-center">
            <CardContent className="flex flex-col items-center justify-center gap-4 px-8 py-12 text-center">
                <div className="text-sm text-muted-foreground flex items-center gap-2">
                    <FlipHorizontal className="h-4 w-4" />
                    Click to show question
                </div>

                <div className="max-w-lg text-xl leading-relaxed font-medium">
                   {card.answer}
                </div>

                <div className="text-sm text-muted-foreground">
                    Answer
                </div>
            </CardContent>
        </div> )}
    </div>
</Card>

                {/* rating buttons */}
                <div className="flex gap-3">
                    <Button
                        variant="outline"
                        className="border-destructive text-destructive hover:bg-destructive/10"
                        onClick={() => handleRate('hard')}
                    >
                        <X className="mr-2 h-4 w-4" />
                        Still Learning
                    </Button>
                    <Button
                        variant="outline"
                        className="border-green-500 text-green-600 hover:bg-green-500/10"
                        onClick={() => handleRate('known')}
                    >
                        <Check className="mr-2 h-4 w-4" />
                        Got It
                    </Button>
                </div>

                {/* score summary */}
                <div className="flex gap-6 text-sm text-muted-foreground">
                    <span className="flex items-center gap-1">
                        <Check className="h-4 w-4 text-green-500" />
                        {knownCount} known
                    </span>
                    <span className="flex items-center gap-1">
                        <X className="h-4 w-4 text-amber-500" />
                        {hardCount} still learning
                    </span>
                </div>
            </div>
        </>
    );
}

SetPractice.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: '/dashboard' },
        { title: 'My Sets', href: '/sets' },
    ],
};
