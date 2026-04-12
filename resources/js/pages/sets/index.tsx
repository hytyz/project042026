import { Head, router } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    CardDescription,
} from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Plus, Search, Trash2, Edit, BookOpen, Play } from 'lucide-react';
import { dashboard } from '@/routes';

interface FlashcardSet {
    id: number;
    title: string;
    created_at: string;
    flashcards: { id: number }[];
    tags: { id: number; name: string }[];
}

interface Tag {
    id: number;
    name: string;
}

export default function SetsIndex() {
    const [sets, setSets] = useState<FlashcardSet[]>([]);
    const [tags, setTags] = useState<Tag[]>([]);
    const [searchQuery, setSearchQuery] = useState('');
    const [selectedTag, setSelectedTag] = useState<number | null>(null);
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        fetchSets();
        fetchTags();
    }, []);

    const fetchSets = () => {
        fetch('/api/sets', {
            headers: { Accept: 'application/json' },
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.success) {
                    setSets(data.data);
                }
                setIsLoading(false);
            });
    };

    const fetchTags = () => {
        fetch('/api/tags', {
            headers: { Accept: 'application/json' },
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.success) {
                    setTags(data.data);
                }
            });
    };

    const deleteSet = (id: number) => {
        if (!confirm('Are you sure you want to delete this set?')) return;

        fetch(`/api/sets/${id}`, {
            method: 'DELETE',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN':
                    document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute('content') || '',
            },
        }).then(() => {
            setSets(sets.filter((s) => s.id !== id));
        });
    };

    const filteredSets = sets.filter((set) => {
        const matchesSearch = set.title
            .toLowerCase()
            .includes(searchQuery.toLowerCase());
        const matchesTag =
            selectedTag === null || set.tags.some((t) => t.id === selectedTag);
        return matchesSearch && matchesTag;
    });

    return (
        <>
            <Head title="My Sets" />
            <div className="flex h-full flex-1 flex-col gap-6 p-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">
                            My Flashcard Sets
                        </h1>
                        <p className="text-muted-foreground">
                            Manage and review all your flashcard sets
                        </p>
                    </div>
                    <Button onClick={() => router.visit(dashboard())}>
                        <Plus className="mr-2 h-4 w-4" />
                        Create New
                    </Button>
                </div>

                <div className="flex flex-col gap-4 sm:flex-row">
                    <div className="relative flex-1">
                        <Search className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            placeholder="Search sets..."
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            className="pl-10"
                        />
                    </div>
                    <div className="flex flex-wrap gap-2">
                        <Badge
                            variant={
                                selectedTag === null ? 'default' : 'outline'
                            }
                            className="cursor-pointer"
                            onClick={() => setSelectedTag(null)}
                        >
                            All
                        </Badge>
                        {tags.map((tag) => (
                            <Badge
                                key={tag.id}
                                variant={
                                    selectedTag === tag.id
                                        ? 'default'
                                        : 'outline'
                                }
                                className="cursor-pointer"
                                onClick={() => setSelectedTag(tag.id)}
                            >
                                {tag.name}
                            </Badge>
                        ))}
                    </div>
                </div>

                {isLoading ? (
                    <div className="py-12 text-center">
                        <p className="text-muted-foreground">Loading...</p>
                    </div>
                ) : filteredSets.length === 0 ? (
                    <div className="py-12 text-center">
                        <BookOpen className="mx-auto h-12 w-12 text-muted-foreground" />
                        <h3 className="mt-4 text-lg font-semibold">
                            No sets found
                        </h3>
                        <p className="text-muted-foreground">
                            {sets.length === 0
                                ? 'Create your first flashcard set to get started'
                                : 'Try adjusting your search or filter'}
                        </p>
                        {sets.length === 0 && (
                            <Button
                                className="mt-4"
                                onClick={() => router.visit(dashboard())}
                            >
                                Create Set
                            </Button>
                        )}
                    </div>
                ) : (
                    <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        {filteredSets.map((set) => (
                            <Card
                                key={set.id}
                                className="transition-shadow hover:shadow-md"
                            >
                                <CardHeader>
                                    <CardTitle className="line-clamp-1">
                                        {set.title}
                                    </CardTitle>
                                    <CardDescription>
                                        {set.flashcards.length} cards ·{' '}
                                        {new Date(
                                            set.created_at,
                                        ).toLocaleDateString()}
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <div className="mb-4 flex flex-wrap gap-2">
                                        {set.tags.map((tag) => (
                                            <Badge
                                                key={tag.id}
                                                variant="secondary"
                                                className="text-xs"
                                            >
                                                {tag.name}
                                            </Badge>
                                        ))}
                                    </div>
                                    <div className="flex gap-2">
                                        <Button
                                            size="sm"
                                            className="flex-1"
                                            onClick={() =>
                                                router.visit(
                                                    `/sets/${set.id}/practice`,
                                                )
                                            }
                                        >
                                            <Play className="mr-2 h-4 w-4" />
                                            Practice
                                        </Button>
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            className="flex-1"
                                            onClick={() =>
                                                router.visit(`/sets/${set.id}`)
                                            }
                                        >
                                            <Edit className="mr-2 h-4 w-4" />
                                            Edit
                                        </Button>
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            className="text-destructive hover:text-destructive"
                                            onClick={() => deleteSet(set.id)}
                                        >
                                            <Trash2 className="h-4 w-4" />
                                        </Button>
                                    </div>
                                </CardContent>
                            </Card>
                        ))}
                    </div>
                )}
            </div>
        </>
    );
}

SetsIndex.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'My Sets', href: '/sets' },
    ],
};
