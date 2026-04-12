import { Head, useForm, router } from '@inertiajs/react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    CardDescription,
} from '@/components/ui/card';
import { Textarea } from '@/components/ui/textarea';
import { Input } from '@/components/ui/input';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Loader2, Sparkles, BookOpen, Wand2 } from 'lucide-react';
import { dashboard } from '@/routes';

interface Flashcard {
    question: string;
    answer: string;
}

export default function Dashboard() {
    const [generatedCards, setGeneratedCards] = useState<Flashcard[]>([]);
    const [isGenerating, setIsGenerating] = useState(false);
    const [activeTab, setActiveTab] = useState('notes');

    const notesForm = useForm({
        content: '',
        type: 'notes',
    });

    const topicForm = useForm({
        content: '',
        type: 'topic',
    });

    const handleGenerate = (e: React.FormEvent) => {
        e.preventDefault();
        setIsGenerating(true);

        const form = activeTab === 'notes' ? notesForm : topicForm;

        fetch('/api/flashcards/generate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN':
                    document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute('content') || '',
            },
            body: JSON.stringify(form.data),
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.success) {
                    setGeneratedCards(data.data);
                }
                setIsGenerating(false);
            })
            .catch(() => {
                setIsGenerating(false);
            });
    };

    const handleReview = () => {
        sessionStorage.setItem(
            'generatedFlashcards',
            JSON.stringify(generatedCards),
        );
        router.visit('/flashcards/review');
    };

    return (
        <>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-6 p-6">
                <div className="flex flex-col gap-2">
                    <h1 className="text-3xl font-bold tracking-tight">
                        Polinotes
                    </h1>
                    <p className="text-muted-foreground">
                        Transform your lecture notes or topics into study
                        flashcards using AI.
                    </p>
                </div>

                <div className="grid gap-6 lg:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Wand2 className="h-5 w-5" />
                                Generate Flashcards
                            </CardTitle>
                            <CardDescription>
                                Enter your lecture notes or a topic to generate
                                flashcards
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <Tabs
                                value={activeTab}
                                onValueChange={setActiveTab}
                            >
                                <TabsList className="grid w-full grid-cols-2">
                                    <TabsTrigger value="notes">
                                        Lecture Notes
                                    </TabsTrigger>
                                    <TabsTrigger value="topic">
                                        Topic
                                    </TabsTrigger>
                                </TabsList>

                                <TabsContent value="notes">
                                    <form
                                        onSubmit={handleGenerate}
                                        className="space-y-4"
                                    >
                                        <Textarea
                                            placeholder="Paste your lecture notes here..."
                                            value={notesForm.data.content}
                                            onChange={(e) =>
                                                notesForm.setData(
                                                    'content',
                                                    e.target.value,
                                                )
                                            }
                                            rows={8}
                                            className="resize-none"
                                        />
                                        <Button
                                            type="submit"
                                            className="w-full"
                                            disabled={
                                                !notesForm.data.content ||
                                                isGenerating
                                            }
                                        >
                                            {isGenerating ? (
                                                <>
                                                    <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                                                    Generating...
                                                </>
                                            ) : (
                                                <>
                                                    <Sparkles className="mr-2 h-4 w-4" />
                                                    Generate from Notes
                                                </>
                                            )}
                                        </Button>
                                    </form>
                                </TabsContent>

                                <TabsContent value="topic">
                                    <form
                                        onSubmit={handleGenerate}
                                        className="space-y-4"
                                    >
                                        <Input
                                            placeholder='Enter a topic like "Tauri vs Electron" or "Development in Rust")'
                                            value={topicForm.data.content}
                                            onChange={(e) =>
                                                topicForm.setData(
                                                    'content',
                                                    e.target.value,
                                                )
                                            }
                                        />
                                        <Button
                                            type="submit"
                                            className="w-full"
                                            disabled={
                                                !topicForm.data.content ||
                                                isGenerating
                                            }
                                        >
                                            {isGenerating ? (
                                                <>
                                                    <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                                                    Generating...
                                                </>
                                            ) : (
                                                <>
                                                    <Sparkles className="mr-2 h-4 w-4" />
                                                    Generate from Topic
                                                </>
                                            )}
                                        </Button>
                                    </form>
                                </TabsContent>
                            </Tabs>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <BookOpen className="h-5 w-5" />
                                Quick Access
                            </CardTitle>
                            <CardDescription>
                                Your study sets and recent activity
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <a
                                href="/sets"
                                className="flex items-center justify-between border p-4 hover:bg-muted"
                            >
                                <div>
                                    <p className="font-medium">
                                        My Flashcard Sets
                                    </p>
                                    <p className="text-sm text-muted-foreground">
                                        View and manage all your sets
                                    </p>
                                </div>
                                <BookOpen className="h-5 w-5 text-muted-foreground" />
                            </a>

                            {generatedCards.length > 0 && (
                                <div className="border border-primary/20 bg-primary/5 p-4">
                                    <p className="font-medium text-primary">
                                        {generatedCards.length} flashcards
                                        generated
                                    </p>
                                    <p className="mb-3 text-sm text-muted-foreground">
                                        Review and save these flashcards to your
                                        collection
                                    </p>
                                    <Button
                                        onClick={handleReview}
                                        size="sm"
                                        className="w-full"
                                    >
                                        Review & Save
                                    </Button>
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>

                {generatedCards.length > 0 && (
                    <Card>
                        <CardHeader>
                            <CardTitle>Generated Flashcards</CardTitle>
                            <CardDescription>
                                Preview of your generated flashcards
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                                {generatedCards
                                    .slice(0, 6)
                                    .map((card, index) => (
                                        <div
                                            key={index}
                                            className="border p-4 transition-colors hover:border-primary/50"
                                        >
                                            <p className="mb-2 line-clamp-2 font-medium">
                                                {card.question}
                                            </p>
                                            <p className="line-clamp-3 text-sm text-muted-foreground">
                                                {card.answer}
                                            </p>
                                        </div>
                                    ))}
                            </div>
                            {generatedCards.length > 6 && (
                                <p className="mt-4 text-center text-sm text-muted-foreground">
                                    +{generatedCards.length - 6} more cards
                                </p>
                            )}
                            <div className="mt-6 flex justify-center">
                                <Button onClick={handleReview}>
                                    Review All & Save
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                )}
            </div>
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [
        {
            title: 'Dashboard',
            href: dashboard(),
        },
    ],
};
