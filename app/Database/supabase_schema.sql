-- ==============================================================================
-- SMART MADANI - MASTER SUPABASE POSTGRESQL SCHEMA (AUDIT REVISION)
-- ==============================================================================
-- Buka Supabase: Dashboard -> Project -> SQL Editor -> New Query -> Tempel dan Jalankan (RUN).
-- Skrip ini idempotent dan aman dijalankan ulang (IF NOT EXISTS & OR REPLACE).
-- ==============================================================================

-- 1. TABEL PROFIL PENGGUNA (GURU & KEPALA SEKOLAH)
CREATE TABLE IF NOT EXISTS public.profiles (
    id UUID PRIMARY KEY REFERENCES auth.users(id) ON DELETE CASCADE,
    nip VARCHAR(50) UNIQUE NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'guru' CHECK (role IN ('guru', 'kepala_sekolah', 'admin')),
    subject_specialty VARCHAR(100),
    phone_number VARCHAR(30),
    bio TEXT,
    avatar_url TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- 2. TABEL PRESENSI GURU (SMART ATTENDANCE)
CREATE TABLE IF NOT EXISTS public.attendances (
    id BIGSERIAL PRIMARY KEY,
    user_id UUID REFERENCES public.profiles(id) ON DELETE CASCADE NOT NULL,
    attendance_date DATE NOT NULL DEFAULT CURRENT_DATE,
    check_in_time TIME,
    check_out_time TIME,
    latitude_in NUMERIC(10,7),
    longitude_in NUMERIC(10,7),
    photo_in_url TEXT,
    status VARCHAR(20) DEFAULT 'hadir' CHECK (status IN ('hadir', 'terlambat', 'izin', 'dinas_luar')),
    notes TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW(),
    UNIQUE(user_id, attendance_date)
);

-- 3. TABEL JURNAL AKTIVITAS 4 KOMPETENSI (OUR ACTIVITY)
CREATE TABLE IF NOT EXISTS public.teacher_activities (
    id BIGSERIAL PRIMARY KEY,
    user_id UUID REFERENCES public.profiles(id) ON DELETE CASCADE NOT NULL,
    activity_date DATE NOT NULL DEFAULT CURRENT_DATE,
    competency_type VARCHAR(30) NOT NULL CHECK (competency_type IN ('pedagogik', 'kepribadian', 'sosial', 'profesional')),
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    deep_learning_pillar VARCHAR(30) DEFAULT 'integrated' CHECK (deep_learning_pillar IN ('mindful', 'meaningful', 'joyful', 'integrated')),
    evidence_file_url TEXT,
    verification_status VARCHAR(20) DEFAULT 'pending' CHECK (verification_status IN ('pending', 'approved', 'revision')),
    verified_by UUID REFERENCES public.profiles(id) ON DELETE SET NULL,
    verification_notes TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Pastikan kolom verification_notes ada jika tabel sudah terbuat sebelumnya
ALTER TABLE public.teacher_activities ADD COLUMN IF NOT EXISTS verification_notes TEXT;
ALTER TABLE public.teacher_activities ADD COLUMN IF NOT EXISTS verified_by UUID REFERENCES public.profiles(id) ON DELETE SET NULL;

-- 4. TABEL REPOSITORI MODUL & INOVASI PEDAGOGIS (OUR CREATIVITY)
CREATE TABLE IF NOT EXISTS public.teacher_creativities (
    id BIGSERIAL PRIMARY KEY,
    user_id UUID REFERENCES public.profiles(id) ON DELETE CASCADE NOT NULL,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(40) NOT NULL CHECK (category IN ('modul_ajar', 'video_pembelajaran', 'lkpd', 'media_interaktif')),
    deep_learning_focus VARCHAR(20) DEFAULT 'all' CHECK (deep_learning_focus IN ('mindful', 'meaningful', 'joyful', 'all')),
    description TEXT,
    file_attachment_url TEXT,
    video_embed_url TEXT,
    is_featured BOOLEAN DEFAULT FALSE,
    curator_notes TEXT,
    curated_by UUID REFERENCES public.profiles(id) ON DELETE SET NULL,
    curated_at TIMESTAMPTZ,
    likes_count INT DEFAULT 0,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

ALTER TABLE public.teacher_creativities ADD COLUMN IF NOT EXISTS is_featured BOOLEAN DEFAULT FALSE;
ALTER TABLE public.teacher_creativities ADD COLUMN IF NOT EXISTS curator_notes TEXT;
ALTER TABLE public.teacher_creativities ADD COLUMN IF NOT EXISTS curated_by UUID REFERENCES public.profiles(id) ON DELETE SET NULL;
ALTER TABLE public.teacher_creativities ADD COLUMN IF NOT EXISTS curated_at TIMESTAMPTZ;

-- 5. TABEL OUR REFLEKSI GURU (KEMENDIKDASMEN 4 LEVEL)
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
    reviewed_by UUID REFERENCES public.profiles(id) ON DELETE SET NULL,
    reviewed_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

ALTER TABLE public.teacher_reflections ADD COLUMN IF NOT EXISTS reviewed_by UUID REFERENCES public.profiles(id) ON DELETE SET NULL;
ALTER TABLE public.teacher_reflections ADD COLUMN IF NOT EXISTS reviewed_at TIMESTAMPTZ;

-- 6. TABEL SUPERVISI & ASESMEN KLINIS KEPALA SEKOLAH (WAJIB DIBUAT)
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
    score_mindful NUMERIC(3,2) CHECK (score_mindful BETWEEN 1.0 AND 4.0),
    score_meaningful NUMERIC(3,2) CHECK (score_meaningful BETWEEN 1.0 AND 4.0),
    score_joyful NUMERIC(3,2) CHECK (score_joyful BETWEEN 1.0 AND 4.0),
    deep_learning_score NUMERIC(3,2) CHECK (deep_learning_score BETWEEN 1.0 AND 4.0),
    coaching_notes TEXT NOT NULL,
    followup_action TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Hapus tabel survei murid jika masih ada
DROP TABLE IF EXISTS public.student_feedbacks CASCADE;

-- ==============================================================================
-- TRIGGER AUTO-INSERT PROFILE DARI USER METADATA (DENGAN ROLE DINAMIS)
-- ==============================================================================
CREATE OR REPLACE FUNCTION public.handle_new_user()
RETURNS TRIGGER AS $$
DECLARE
    assigned_role VARCHAR(20);
BEGIN
    assigned_role := COALESCE(NEW.raw_user_meta_data->>'role', 'guru');
    IF assigned_role NOT IN ('guru', 'kepala_sekolah', 'admin') THEN
        assigned_role := 'guru';
    END IF;

    INSERT INTO public.profiles (id, nip, full_name, role, subject_specialty, phone_number)
    VALUES (
        NEW.id,
        COALESCE(NEW.raw_user_meta_data->>'nip', ''),
        COALESCE(NEW.raw_user_meta_data->>'full_name', 'Pengguna Baru'),
        assigned_role,
        COALESCE(NEW.raw_user_meta_data->>'subject_specialty', ''),
        COALESCE(NEW.raw_user_meta_data->>'phone_number', '')
    )
    ON CONFLICT (id) DO UPDATE SET
        role = EXCLUDED.role,
        nip = EXCLUDED.nip,
        full_name = EXCLUDED.full_name,
        subject_specialty = EXCLUDED.subject_specialty,
        phone_number = EXCLUDED.phone_number;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

DROP TRIGGER IF EXISTS on_auth_user_created ON auth.users;
CREATE TRIGGER on_auth_user_created
    AFTER INSERT ON auth.users
    FOR EACH ROW EXECUTE FUNCTION public.handle_new_user();

-- ==============================================================================
-- ROW LEVEL SECURITY (RLS) POLICIES
-- ==============================================================================
ALTER TABLE public.profiles ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.attendances ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.teacher_activities ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.teacher_creativities ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.teacher_reflections ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.kbm_supervisions ENABLE ROW LEVEL SECURITY;

-- 1. Profiles
DROP POLICY IF EXISTS "Public can view profiles" ON public.profiles;
CREATE POLICY "Public can view profiles" ON public.profiles FOR SELECT USING (true);
DROP POLICY IF EXISTS "Users can update own profile" ON public.profiles;
CREATE POLICY "Users can update own profile" ON public.profiles FOR UPDATE USING (auth.uid() = id);
DROP POLICY IF EXISTS "Service role full access profiles" ON public.profiles;
CREATE POLICY "Service role full access profiles" ON public.profiles FOR ALL USING (auth.role() = 'service_role');

-- 2. Attendances
DROP POLICY IF EXISTS "Users manage own attendance" ON public.attendances;
CREATE POLICY "Users manage own attendance" ON public.attendances FOR ALL USING (auth.uid() = user_id);
DROP POLICY IF EXISTS "Service role full access attendances" ON public.attendances;
CREATE POLICY "Service role full access attendances" ON public.attendances FOR ALL USING (auth.role() = 'service_role');

-- 3. Activities
DROP POLICY IF EXISTS "Users manage own activities" ON public.teacher_activities;
CREATE POLICY "Users manage own activities" ON public.teacher_activities FOR ALL USING (auth.uid() = user_id);
DROP POLICY IF EXISTS "Service role full access activities" ON public.teacher_activities;
CREATE POLICY "Service role full access activities" ON public.teacher_activities FOR ALL USING (auth.role() = 'service_role');

-- 4. Creativities
DROP POLICY IF EXISTS "Anyone can view creativities" ON public.teacher_creativities;
CREATE POLICY "Anyone can view creativities" ON public.teacher_creativities FOR SELECT USING (true);
DROP POLICY IF EXISTS "Users manage own creativities" ON public.teacher_creativities;
CREATE POLICY "Users manage own creativities" ON public.teacher_creativities FOR ALL USING (auth.uid() = user_id);
DROP POLICY IF EXISTS "Service role full access creativities" ON public.teacher_creativities;
CREATE POLICY "Service role full access creativities" ON public.teacher_creativities FOR ALL USING (auth.role() = 'service_role');

-- 5. Reflections
DROP POLICY IF EXISTS "Users manage own reflections" ON public.teacher_reflections;
CREATE POLICY "Users manage own reflections" ON public.teacher_reflections FOR ALL USING (auth.uid() = user_id);
DROP POLICY IF EXISTS "Service role full access reflections" ON public.teacher_reflections;
CREATE POLICY "Service role full access reflections" ON public.teacher_reflections FOR ALL USING (auth.role() = 'service_role');

-- 6. Supervisi
DROP POLICY IF EXISTS "Public can view supervisions" ON public.kbm_supervisions;
CREATE POLICY "Public can view supervisions" ON public.kbm_supervisions FOR SELECT USING (true);
DROP POLICY IF EXISTS "Service role full access supervisions" ON public.kbm_supervisions;
CREATE POLICY "Service role full access supervisions" ON public.kbm_supervisions FOR ALL USING (auth.role() = 'service_role');
