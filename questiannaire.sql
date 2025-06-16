-- Insert Visual learning style questions
INSERT INTO `questionaires` (`admin_id`, `questionaire`, `questionaire_options`, `date_created`) VALUES
('1', 'I remember something better if I write it down.', 'visual', CURRENT_DATE()),
('1', 'If I am taking a test, my brain will have a picture of the textbook page and where the answer is located.', 'visual', CURRENT_DATE()),
('1', 'I find it easier to focus and understand when my environment is visually quiet.', 'visual', CURRENT_DATE()),
('1', 'I prefer doing my work alone without any interference.', 'visual', CURRENT_DATE()),
('1', 'I enjoy studying notes that use colourful fonts or highlighted keywords.', 'visual', CURRENT_DATE()),
('1', 'I focus better when notes are neatly organized and clearly written.', 'visual', CURRENT_DATE());

-- Insert auditory learning style questions
INSERT INTO `questionaires` (`admin_id`, `questionaire`, `questionaire_options`, `date_created`) VALUES
('1', 'I understand better if someone told me how to do it, rather than having to read the thing by myself.', 'auditory', CURRENT_DATE()),
('1', 'I can remember things that I hear, rather than things that I see or read.', 'auditory', CURRENT_DATE()),
('1', 'I can recall what has been said by the instructor and write it down in my notes.', 'auditory', CURRENT_DATE()),
('1', 'I tend to listen to what the person is saying, rather than reading the subtitles while watching a movie.', 'auditory', CURRENT_DATE()),
('1', 'When I try to recall something, I usually hear a voice in my head repeating the information.', 'auditory', CURRENT_DATE()),
('1', 'I prefer listening to audiobooks over reading text.', 'auditory', CURRENT_DATE());

-- Insert read_write learning style questions
INSERT INTO `questionaires` (`admin_id`, `questionaire`, `questionaire_options`, `date_created`) VALUES
('1', 'I learn best by reading instructions carefully before starting a task.', 'read_write', CURRENT_DATE()),
('1', 'I prefer reading and writing about a process to understand it rather than seeing it demonstrated.', 'read_write', CURRENT_DATE()),
('1', 'I solve problems more effectively by researching and writing down solutions.', 'read_write', CURRENT_DATE()),
('1', 'I express my thoughts better through writing than speaking.', 'read_write', CURRENT_DATE()),
('1', 'I enjoy studying in a quiet space with books, notes, and reading materials.', 'read_write', CURRENT_DATE()),
('1', 'Writing summaries and making lists help me understand and remember topics.', 'read_write', CURRENT_DATE());