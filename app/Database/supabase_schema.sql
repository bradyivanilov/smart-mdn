-- ==============================================================================
-- SMART MADANI - SUPABASE POSTGRESQL SCHEMA (PRODUCTION & DYNAMIC)
-- ==============================================================================

-- 1. Tabel Profil Pengguna (Guru, Kepala Sekolah, Admin)
CREATE TABLE IF NOT EXISTS public.profiles (
    id UUID PRIMARY KEY REFERENCES auth.users(id) ON DELETE CASCADE,
    nip VARCHAR(50) UNIQUE NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    role VARCHAR(20) DEFAULT 'guru' CHECK (role IN ('guru','kepala_sekolah','admin')),
    subject_specialty VARCHAR(100),
    phone_number VARCHAR(30),
    bio TEXT,
    avatar_url TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- 2. Tabel Presensi Guru (Smart Attendance)
CREATE TABLE IF NOT EXISTS public.attendances (
    id BIGSERIAL PRIMARY KEY,
    user_id UUID REFERENCES public.profiles(id) ON DELETE CASCADE NOT NULL,
    attendance_date DATE NOT NULL DEFAULT CURRENT_DATE,
    check_in_time TIME,
    check_out_time TIME,
    latitude_in NUMERIC(10,7),
    longitude_in NUMERIC(10,7),
    photo_in_url TEXT,
    status VARCHAR(20) DEFAULT 'hadir' CHECK (status IN ('hadir','terlambat','izin','dinas_luar')),
    notes TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW(),
    UNIQUE(user_id, attendance_date)
);

-- 3. Tabel Jurnal Aktivitas 4 Kompetensi (Our Activity)
CREATE TABLE IF NOT EXISTS public.teacher_activities (
    id BIGSERIAL PRIMARY KEY,
    user_id UUID REFERENCES public.profiles(id) ON DELETE CASCADE NOT NULL,
    activity_date DATE NOT NULL DEFAULT CURRENT_DATE,
    competency_type VARCHAR(30) NOT NULL CHECK (competency_type IN ('pedagogik','kepribadian','sosial','profesional')),
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    deep_learning_pillar VARCHAR(30) DEFAULT 'integrated' CHECK (deep_learning_pillar IN ('mindful','meaningful','joyful','integrated')),
    evidence_file_url TEXT,
    verification_status VARCHAR(20) DEFAULT 'pending' CHECK (verification_status IN ('pending','approved','revision')),
    verified_by UUID REFERENCES public.profiles(id),
    verification_notes TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- 4. Tabel Repositori Modul & Inovasi Guru (Our Creativity)
CREATE TABLE IF NOT EXISTS public.teacher_creativities (
    id BIGSERIAL PRIMARY KEY,
    user_id UUID REFERENCES public.profiles(id) ON DELETE CASCADE NOT NULL,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(40) NOT NULL CHECK (category IN ('modul_ajar','video_pembelajaran','lkpd','media_interaktif')),
    deep_learning_focus VARCHAR(20) DEFAULT 'all' CHECK (deep_learning_focus IN ('mindful','meaningful','joyful','all')),
    description TEXT,
    file_attachment_url TEXT,
    video_embed_url TEXT,
    is_featured BOOLEAN DEFAULT FALSE,
    likes_count INT DEFAULT 0,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- 5. Tabel Our Refleksi (Kemendikdasmen Framework 4 Level)
CREATE TABLE IF NOT EXISTS public.teacher_reflections (
    id BIGSERIAL PRIMARY KEY,
    user_id UUID REFERENCES public.profiles(id) ON DELETE CASCADE NOT NULL,
    activity_id BIGINT REFERENCES public.teacher_activities(id) ON DELETE SET NULL,
    reflection_date DATE NOT NULL DEFAULT CURRENT_DATE,
    situation_analysis TEXT NOT NULL,
    challenge_identification TEXT NOT NULL,
    action_plan TEXT NOT NULL,
    competency_level INT DEFAULT 1 CHECK (competency_level BETWEEN 1 AND 4),
    principal_feedback TEXT,
    reviewed_by UUID REFERENCES public.profiles(id),
    reviewed_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- 6. Tabel Suara Murid (Student Feedback Pulse - Anonim via QR Code)
CREATE TABLE IF NOT EXISTS public.student_feedbacks (
    id BIGSERIAL PRIMARY KEY,
    teacher_id UUID REFERENCES public.profiles(id) ON DELETE CASCADE NOT NULL,
    subject_name VARCHAR(100),
    grade_class VARCHAR(50),
    joyful_score INT NOT NULL CHECK (joyful_score BETWEEN 1 AND 5),
    meaningful_score INT NOT NULL CHECK (meaningful_score BETWEEN 1 AND 5),
    mindful_score INT NOT NULL CHECK (mindful_score BETWEEN 1 AND 5),
    student_note TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- 7. Tabel Supervisi & Asesmen Klinis Kepala Sekolah (PRD Section 4 Modul 5)
CREATE TABLE IF NOT EXISTS public.kbm_supervisions (
    id BIGSERIAL PRIMARY KEY,
    teacher_id UUID REFERENCES public.profiles(id) ON DELETE CASCADE NOT NULL,
    supervisor_id UUID REFERENCES public.profiles(id) ON DELETE RESTRICT NOT NULL,
    supervision_date DATE NOT NULL DEFAULT CURRENT_DATE,
    class_name VARCHAR(50) NOT NULL,
    subject VARCHAR(100) NOT NULL,
    score_pedagogic NUMERIC(3,2) CHECK (score_pedagogic BETWEEN 1.0 AND 4.0),
    score_personality NUMERIC(3,2) CHECK (score_personality BETWEEN 1.0 AND 4.0),
    score_social NUMERIC(3,2) CHECK (score_social BETWEEN 1.0 AND 4.0),
    score_professional NUMERIC(3,2) CHECK (score_professional BETWEEN 1.0 AND 4.0),
    deep_learning_score NUMERIC(3,2) CHECK (deep_learning_score BETWEEN 1.0 AND 4.0),
    coaching_notes TEXT NOT NULL,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Trigger auto-insert profile
CREATE OR REPLACE FUNCTION public.handle_new_user()
RETURNS TRIGGER AS $$
BEGIN
    INSERT INTO public.profiles (id, nip, full_name, role, subject_specialty, phone_number)
    VALUES (
        NEW.id,
        COALESCE(NEW.raw_user_meta_data->>'nip', ''),
        COALESCE(NEW.raw_user_meta_data->>'full_name', 'Pengguna Baru'),
        COALESCE(NEW.raw_user_meta_data->>'role', 'guru'),
        COALESCE(NEW.raw_user_meta_data->>'subject_specialty', ''),
        COALESCE(NEW.raw_user_meta_data->>'phone_number', '')
    )
    ON CONFLICT (id) DO UPDATE SET
        nip = EXCLUDED.nip,
        full_name = EXCLUDED.full_name,
        role = EXCLUDED.role,
        subject_specialty = EXCLUDED.subject_specialty;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

DROP TRIGGER IF EXISTS on_auth_user_created ON auth.users;
CREATE TRIGGER on_auth_user_created
    AFTER INSERT ON auth.users
    FOR EACH ROW EXECUTE FUNCTION public.handle_new_user();

-- Enable RLS
ALTER TABLE public.profiles ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.attendances ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.teacher_activities ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.teacher_creativities ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.teacher_reflections ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.student_feedbacks ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.kbm_supervisions ENABLE ROW LEVEL SECURITY;

-- Public Read Profiles (untuk e-CV & nama guru)
DROP POLICY IF EXISTS "Public can view profiles" ON public.profiles;
CREATE POLICY "Public can view profiles" ON public.profiles FOR SELECT USING (true);

DROP POLICY IF EXISTS "Users can update own profile" ON public.profiles;
CREATE POLICY "Users can update own profile" ON public.profiles FOR UPDATE USING (auth.uid() = id);

DROP POLICY IF EXISTS "Service role full access profiles" ON public.profiles;
CREATE POLICY "Service role full access profiles" ON public.profiles FOR ALL USING (auth.role() = 'service_role');

-- Attendances
DROP POLICY IF EXISTS "Users manage own attendance" ON public.attendances;
CREATE POLICY "Users manage own attendance" ON public.attendances FOR ALL USING (auth.uid() = user_id);

DROP POLICY IF EXISTS "Service role full access attendances" ON public.attendances;
CREATE POLICY "Service role full access attendances" ON public.attendances FOR ALL USING (auth.role() = 'service_role');

-- Activities
DROP POLICY IF EXISTS "Users manage own activities" ON public.teacher_activities;
CREATE POLICY "Users manage own activities" ON public.teacher_activities FOR ALL USING (auth.uid() = user_id);

DROP POLICY IF EXISTS "Service role full access activities" ON public.teacher_activities;
CREATE POLICY "Service role full access activities" ON public.teacher_activities FOR ALL USING (auth.role() = 'service_role');

-- Creativities
DROP POLICY IF EXISTS "Anyone can view creativities" ON public.teacher_creativities;
CREATE POLICY "Anyone can view creativities" ON public.teacher_creativities FOR SELECT USING (true);

DROP POLICY IF EXISTS "Users manage own creativities" ON public.teacher_creativities;
CREATE POLICY "Users manage own creativities" ON public.teacher_creativities FOR ALL USING (auth.uid() = user_id);

DROP POLICY IF EXISTS "Service role full access creativities" ON public.teacher_creativities;
CREATE POLICY "Service role full access creativities" ON public.teacher_creativities FOR ALL USING (auth.role() = 'service_role');

-- Reflections
DROP POLICY IF EXISTS "Users manage own reflections" ON public.teacher_reflections;
CREATE POLICY "Users manage own reflections" ON public.teacher_reflections FOR ALL USING (auth.uid() = user_id);

DROP POLICY IF EXISTS "Service role full access reflections" ON public.teacher_reflections;
CREATE POLICY "Service role full access reflections" ON public.teacher_reflections FOR ALL USING (auth.role() = 'service_role');

-- Student feedbacks
DROP POLICY IF EXISTS "Anyone can submit feedback" ON public.student_feedbacks;
CREATE POLICY "Anyone can submit feedback" ON public.student_feedbacks FOR INSERT WITH CHECK (true);

DROP POLICY IF EXISTS "Service role full access feedbacks" ON public.student_feedbacks;
CREATE POLICY "Service role full access feedbacks" ON public.student_feedbacks FOR ALL USING (auth.role() = 'service_role');

-- Supervisi
DROP POLICY IF EXISTS "Service role full access supervisions" ON public.kbm_supervisions;
CREATE POLICY "Service role full access supervisions" ON public.kbm_supervisions FOR ALL USING (auth.role() = 'service_role');
