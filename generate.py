import mysql.connector
import pandas as pd
import numpy as np

# Database credentials
db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'electre'
}

cnx = None  # Initialize cnx to None

try:
    # Connect to the database
    cnx = mysql.connector.connect(**db_config)

    if cnx.is_connected():  # Check if connection is successful
        print("Connected to database")
        cursor = cnx.cursor()

        cnx = mysql.connector.connect(**db_config)
        cursor = cnx.cursor()

        # Fetch alternatives
        query_alternatif = "SELECT kode_alternatif, nama_alternatif FROM alternatif"
        cursor.execute(query_alternatif)
        alternatives_data = cursor.fetchall()
        alternatives = [row for row in alternatives_data]

        # Fetch criteria and weights
        query_kriteria = "SELECT kode_kriteria, nama_kriteria, bobot FROM kriteria"
        cursor.execute(query_kriteria)
        kriteria_data = cursor.fetchall()
        criteria = [row for row in kriteria_data]
        weights = {row: row for row in kriteria_data}

        # Fetch penilaian (evaluation) data
        query_penilaian = "SELECT kode_alternatif, kode_kriteria, nilai FROM penilaian"
        cursor.execute(query_penilaian)
        penilaian_data = cursor.fetchall()

        # Create data dictionary
        data = {}
        for kriteria in criteria:
            data[kriteria] =[]
            for alternatif in alternatives:
                nilai = next((row for row in penilaian_data if row == alternatif and row == kriteria), 0)
                data[kriteria].append(nilai)

        # Convert weights to array
        weights_array = np.array([weights[c] for c in criteria])

        # Create DataFrame
        df = pd.DataFrame(data, index=alternatives)

        # --- Step 1: Normalization ---

        df_norm = df / np.sqrt(np.sum(df**2, axis=0))

        # --- Step 2: Weighted Matrix ---

        df_weighted = df_norm * weights_array

        # --- Step 3: Concordance and Discordance Sets ---

        concordance_sets = {}
        discordance_sets = {}
        for k in df_weighted.index:
            concordance_sets[k] = {}
            discordance_sets[k] = {}
            for l in df_weighted.index:
                if k!= l:
                    concordance_set = set()
                    discordance_set = set()
                    for j in range(len(criteria)):
                        if df_weighted.loc[k][j] >= df_weighted.loc[l][j]:
                            concordance_set.add(criteria[j])
                        else:
                            discordance_set.add(criteria[j])
                    concordance_sets[k][l] = concordance_set
                    discordance_sets[k][l] = discordance_set

        # --- Step 4: Concordance and Discordance Matrices ---

        concordance_matrix = np.zeros((len(df_weighted), len(df_weighted)))
        discordance_matrix = np.zeros((len(df_weighted), len(df_weighted)))
        for k in range(len(df_weighted)):
            for l in range(len(df_weighted)):
                if k!= l:
                    concordance_index = sum(weights[j] for j in concordance_sets[alternatives[k]][alternatives[l]])
                    concordance_matrix[k, l] = concordance_index

                    max_diff = max(abs(df_weighted.loc[alternatives[k]][j] - df_weighted.loc[alternatives[l]][j]) for j in discordance_sets[alternatives[k]][alternatives[l]])
                    max_all_diff = max(abs(df_weighted.loc[alternatives[k]][j] - df_weighted.loc[alternatives[l]][j]) for j in criteria)
                    discordance_index = max_diff / max_all_diff if max_all_diff!= 0 else 0
                    discordance_matrix[k, l] = discordance_index

        # --- Step 5: Thresholds ---

        c_threshold = np.sum(concordance_matrix) / (len(concordance_matrix) * (len(concordance_matrix) - 1))
        d_threshold = np.sum(discordance_matrix) / (len(discordance_matrix) * (len(discordance_matrix) - 1))

        # --- Step 6: Dominance Matrices ---

        f_matrix = (concordance_matrix >= c_threshold).astype(int)
        g_matrix = (discordance_matrix <= d_threshold).astype(int)

        # --- Step 7: Aggregate Dominance Matrix ---

        e_matrix = f_matrix * g_matrix

        # --- Step 8: Eliminate Less Favorable Alternatives ---

        remaining_alternatives_indices = list(range(len(e_matrix)))
        for k in range(len(e_matrix)):
            for l in range(len(e_matrix)):
                if k!= l and e_matrix[k, l] == 0 and e_matrix[l, k] == 1:
                    if k in remaining_alternatives_indices:
                        remaining_alternatives_indices.remove(k)

        # --- Step 9: Final Ranking ---

        dominance_counts = [np.sum(e_matrix[k,:]) for k in remaining_alternatives_indices]
        ranked_indices = np.argsort(dominance_counts)[::-1]
        ranking = [remaining_alternatives_indices[i] for i in ranked_indices]

        # --- Output to Excel ---
        with pd.ExcelWriter('electre_results.xlsx') as writer:
            df.to_excel(writer, sheet_name='Original Data')
            df_norm.to_excel(writer, sheet_name='Normalized Matrix')
            df_weighted.to_excel(writer, sheet_name='Weighted Matrix')
            pd.DataFrame(concordance_matrix, index=alternatives, columns=alternatives).to_excel(writer, sheet_name='Concordance Matrix')
            pd.DataFrame(discordance_matrix, index=alternatives, columns=alternatives).to_excel(writer, sheet_name='Discordance Matrix')
            pd.DataFrame(f_matrix, index=alternatives, columns=alternatives).to_excel(writer, sheet_name='Concordance Dominance')
            pd.DataFrame(g_matrix, index=alternatives, columns=alternatives).to_excel(writer, sheet_name='Discordance Dominance')
            pd.DataFrame(e_matrix, index=alternatives, columns=alternatives).to_excel(writer, sheet_name='Aggregate Dominance')
            pd.DataFrame({'Rank': range(1, len(ranking) + 1), 'Alternative': [alternatives[i] for i in ranking]}).to_excel(writer, sheet_name='Final Ranking', index=False)

        print("Calculations and results saved to 'electre_results.xlsx'")

    else:
        print("Failed to connect to database")

except mysql.connector.Error as err:
    print(f"Database error: {err}")

finally:
    if cnx and cnx.is_connected():
        cursor.close()
        cnx.close()
        print("Database connection closed.")