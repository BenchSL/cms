import React, { useState } from "react";
import { Calendar, momentLocalizer } from "react-big-calendar";
import "react-big-calendar/lib/css/react-big-calendar.css";
import { Box, Typography, Button, CircularProgress, Modal, Card, CardContent } from "@mui/material";
import moment from "moment";

const localizer = momentLocalizer(moment);

interface Event {
    id: string;
    designation: string;
    title: string;
    description?: string;
    event_date: string;
}

const CalendarEvents: React.FC = () => {
    const [events, setEvents] = useState<Event[]>([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [selectedEvent, setSelectedEvent] = useState<Event | null>(null); // Track the selected event
    const [modalOpen, setModalOpen] = useState(false); // Control modal state

    const fetchEvents = async () => {
        setLoading(true);
        setError(null);

        try {
            console.info('before api call');
            const response = await fetch("/api/calendar-events");
            console.info('After API call response [' + response + ']');
            const data = await response.json();

            if (response.ok) {
                const mappedEvents = data.events.map((event: Event) => ({
                    id: event.designation,
                    title: event.title,
                    start: new Date(event.event_date),
                    end: new Date(event.event_date),
                    description: event.description,
                }));
                setEvents(mappedEvents);
            } else {
                throw new Error(data.error || "Failed to fetch events");
            }
        } catch (err) {
            setError(err.message);
        } finally {
            setLoading(false);
        }
    };

    const handleSelectEvent = (event: Event) => {
        setSelectedEvent(event);
        setModalOpen(true);
    };

    const handleCloseModal = () => {
        setModalOpen(false);
        setSelectedEvent(null);
    };

    return (
        <Box sx={{ padding: "20px", maxWidth: "1200px", margin: "0 auto" }}>
            <Typography variant="h4" gutterBottom>
                Calendar Events
            </Typography>

            <Button
                variant="contained"
                color="primary"
                onClick={fetchEvents}
                disabled={loading}
                sx={{ marginBottom: "20px" }}
            >
                {loading ? <CircularProgress size={24} /> : "Fetch Events"}
            </Button>

            {error && (
                <Typography variant="body1" color="error" gutterBottom>
                    {error}
                </Typography>
            )}

            {events.length > 0 ? (
                <>
                    <Calendar
                        localizer={localizer}
                        events={events}
                        startAccessor="start"
                        endAccessor="end"
                        style={{ height: 500, margin: "50px" }}
                        onSelectEvent={handleSelectEvent}
                    />

                    <Modal open={modalOpen} onClose={handleCloseModal}>
                        <Box
                            sx={{
                                position: 'absolute',
                                top: '50%',
                                left: '50%',
                                transform: 'translate(-50%, -50%)',
                                width: 400,
                                bgcolor: 'background.paper',
                                boxShadow: 24,
                                p: 4,
                                borderRadius: '8px'
                            }}
                        >
                            {selectedEvent && (
                                <Card>
                                    <CardContent>
                                        <Typography variant="h5" gutterBottom>
                                            {selectedEvent.title}
                                        </Typography>
                                        <Typography variant="body2" color="textSecondary">
                                            {selectedEvent.description || 'No description provided.'}
                                        </Typography>
                                        <Typography variant="body2" color="textSecondary">
                                            Event Date: {moment(selectedEvent.event_date).format('MMMM Do YYYY, h:mm a')}
                                        </Typography>
                                    </CardContent>
                                </Card>
                            )}
                        </Box>
                    </Modal>
                </>
            ) : (
                !loading && <Typography variant="body1">No events found in the database.</Typography>
            )}
        </Box>
    );
};

export default CalendarEvents;
