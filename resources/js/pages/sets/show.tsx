import { Head, useForm, router } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Badge } from '@/components/ui/badge';
import { Plus, Save, ArrowLeft, Trash2, Tag } from 'lucide-react';
import { dashboard } from '@/routes';

interface Flashcard {
    id?: number;
    question: string;
    answer: string;
}

interface TagType {
    id: number;
    name: string;
}

interface SetData {
    id: number;
    title: string;
    flashcards: Flashcard[];
    tags: TagType[];
}

export default function SetShow({ id }: { id: string }) {
    const [setData, setSetData] = useState<SetData | null>(null);
    const [tags, setTags] = useState<TagType[]>([]);
    const [selectedTagIds, setSelectedTagIds] = useState<number[]>([]);
    const [newTagName, setNewTagName] = useState('');
    const [isLoading, setIsLoading] = useState(true);
    const [isSaving, setIsSaving] = useState(false);

    const form = useForm({
        title: '',
        flashcards: [] as Flashcard[],
        tag_ids: [] as number[],
    });

    useEffect(() => {
        fetchSet();
        fetchTags();
    }, [id]);

    const fetchSet = () => {
        fetch(`/api/sets/${id}`, {
            headers: { Accept: 'application/json' },
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.success) {
                    setSetData(data.data);
                    form.setData({
                        title: data.data.title,
                        flashcards: data.data.flashcards,
                        tag_ids: data.data.tags.map((t: TagType) => t.id),
                    });
                    setSelectedTagIds(data.data.tags.map((t: TagType) => t.id));
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

    const updateFlashcard = (
        index: number,
        field: keyof Flashcard,
        value: string,
    ) => {
        const updated = [...form.data.flashcards];
        updated[index] = { ...updated[index], [field]: value };
        form.setData('flashcards', updated);
    };

    const removeFlashcard = (index: number) => {
        const updated = form.data.flashcards.filter((_, i) => i !== index);
        form.setData('flashcards', updated);
    };

    const addFlashcard = () => {
        form.setData('flashcards', [
            ...form.data.flashcards,
            { question: '', answer: '' },
        ]);
    };

    const toggleTag = (tagId: number) => {
        const updated = selectedTagIds.includes(tagId)
            ? selectedTagIds.filter((id) => id !== tagId)
            : [...selectedTagIds, tagId];
        setSelectedTagIds(updated);
        form.setData('tag_ids', updated);
    };

    const createTag = () => {
        if (!newTagName.trim()) return;

        fetch('/api/tags', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN':
                    document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute('content') || '',
            },
            body: JSON.stringify({ name: newTagName }),
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.success) {
                    setTags([...tags, data.data]);
                    setSelectedTagIds([...selectedTagIds, data.data.id]);
                    form.setData('tag_ids', [
                        ...form.data.tag_ids,
                        data.data.id,
                    ]);
                    setNewTagName('');
                }
            });
    };

    const handleSave = async (e: React.FormEvent) => {
        e.preventDefault();

        setIsSaving(true);

        try {
            const response = await fetch(`/api/sets/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN':
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute('content') || '',
                },
                body: JSON.stringify(form.data),
            });

            if (!response.ok) {
                throw new Error(`Update failed with status ${response.status}`);
            }

            router.visit('/sets');
        } catch (error) {
            console.error(error);
        } finally {
            setIsSaving(false);
        }
    };

    if (isLoading) {
        return (
            <>
                <Head title="Edit Set" />
                <div className="flex h-full items-center justify-center">
                    <p>Loading...</p>
                </div>
            </>
        );
    }

    return (
        <>
            <Head title="Edit Set" />
            <div className="flex h-full flex-1 flex-col gap-6 p-6">
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <Button
                            variant="ghost"
                            size="icon"
                            onClick={() => router.visit('/sets')}
                        >
                            <ArrowLeft className="h-5 w-5" />
                        </Button>
                        <div>
                            <h1 className="text-2xl font-bold tracking-tight">
                                Edit Set
                            </h1>
                            <p className="text-muted-foreground">
                                Modify your flashcard set
                            </p>
                        </div>
                    </div>
                </div>

                <form onSubmit={handleSave} className="space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>Set Details</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div>
                                <label className="text-sm font-medium">
                                    Title
                                </label>
                                <Input
                                    value={form.data.title}
                                    onChange={(e) =>
                                        form.setData('title', e.target.value)
                                    }
                                    placeholder="Enter a title for this set"
                                    className="mt-1"
                                />
                            </div>

                            <div>
                                <label className="flex items-center gap-2 text-sm font-medium">
                                    <Tag className="h-4 w-4" />
                                    Tags
                                </label>
                                <div className="mt-2 flex flex-wrap gap-2">
                                    {tags.map((tag) => (
                                        <Badge
                                            key={tag.id}
                                            variant={
                                                selectedTagIds.includes(tag.id)
                                                    ? 'default'
                                                    : 'outline'
                                            }
                                            className="cursor-pointer"
                                            onClick={() => toggleTag(tag.id)}
                                        >
                                            {tag.name}
                                        </Badge>
                                    ))}
                                </div>
                                <div className="mt-2 flex gap-2">
                                    <Input
                                        value={newTagName}
                                        onChange={(e) =>
                                            setNewTagName(e.target.value)
                                        }
                                        placeholder="New tag name"
                                        className="w-48"
                                    />
                                    <Button
                                        type="button"
                                        variant="outline"
                                        onClick={createTag}
                                    >
                                        Add Tag
                                    </Button>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <div className="space-y-4">
                        <div className="flex items-center justify-between">
                            <h2 className="text-lg font-semibold">
                                Flashcards ({form.data.flashcards.length})
                            </h2>
                            <Button
                                type="button"
                                variant="outline"
                                onClick={addFlashcard}
                            >
                                <Plus className="mr-2 h-4 w-4" />
                                Add Card
                            </Button>
                        </div>

                        {form.data.flashcards.map((card, index) => (
                            <Card key={index}>
                                <CardContent className="pt-6">
                                    <div className="flex items-start gap-4">
                                        <span className="mt-2 text-sm text-muted-foreground">
                                            {index + 1}
                                        </span>
                                        <div className="flex-1 space-y-4">
                                            <div>
                                                <label className="text-sm font-medium">
                                                    Question
                                                </label>
                                                <Textarea
                                                    value={card.question}
                                                    onChange={(e) =>
                                                        updateFlashcard(
                                                            index,
                                                            'question',
                                                            e.target.value,
                                                        )
                                                    }
                                                    rows={2}
                                                    className="mt-1"
                                                />
                                            </div>
                                            <div>
                                                <label className="text-sm font-medium">
                                                    Answer
                                                </label>
                                                <Textarea
                                                    value={card.answer}
                                                    onChange={(e) =>
                                                        updateFlashcard(
                                                            index,
                                                            'answer',
                                                            e.target.value,
                                                        )
                                                    }
                                                    rows={2}
                                                    className="mt-1"
                                                />
                                            </div>
                                        </div>
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="icon"
                                            onClick={() =>
                                                removeFlashcard(index)
                                            }
                                        >
                                            <Trash2 className="h-4 w-4 text-destructive" />
                                        </Button>
                                    </div>
                                </CardContent>
                            </Card>
                        ))}
                    </div>

                    <div className="flex justify-end gap-4">
                        <Button
                            type="button"
                            variant="outline"
                            onClick={() => router.visit('/sets')}
                        >
                            Cancel
                        </Button>
                        <Button
                            type="submit"
                            disabled={
                                isSaving ||
                                !form.data.title ||
                                form.data.flashcards.length === 0
                            }
                        >
                            <Save className="mr-2 h-4 w-4" />
                            Save Changes
                        </Button>
                    </div>
                </form>
            </div>
        </>
    );
}

SetShow.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'My Sets', href: '/sets' },
        { title: 'Edit Set', href: '#' },
    ],
};
